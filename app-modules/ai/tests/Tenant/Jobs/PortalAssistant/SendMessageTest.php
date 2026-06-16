<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Ai\Enums\AiModel;
use AidingApp\Ai\Events\PortalAssistant\PortalAssistantMessageChunk;
use AidingApp\Ai\Jobs\PortalAssistant\SendMessage;
use AidingApp\Ai\Models\PortalAssistantMessage;
use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Ai\Services\TestAiService;
use AidingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AidingApp\Ai\Support\StreamingChunks\Finish;
use AidingApp\Ai\Support\StreamingChunks\Text;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $settings = app(AiIntegratedAssistantSettings::class);
    $settings->default_model = AiModel::Test;
    $settings->save();
});

it('surfaces the rate limit reset time and does not persist a partial response when the stream is rate limited', function () {
    Event::fake([PortalAssistantMessageChunk::class]);

    $thread = PortalAssistantThread::factory()->create();

    $service = Mockery::mock(TestAiService::class)->makePartial();
    $service->shouldReceive('streamRaw')->andReturn(function () {
        yield new Text('A partial answer that gets cut off');

        yield new Finish(rateLimitResetsAt: now()->addSeconds(30));
    });
    app()->instance(TestAiService::class, $service);

    dispatch_sync(new SendMessage($thread, 'Hello'));

    expect(PortalAssistantMessage::query()->where('is_assistant', false)->count())->toBe(1);
    expect(PortalAssistantMessage::query()->where('is_assistant', true)->count())->toBe(0);

    Event::assertDispatched(
        PortalAssistantMessageChunk::class,
        fn (PortalAssistantMessageChunk $event): bool => $event->thread->is($thread)
            && $event->isComplete === false
            && $event->rateLimitResetsAt !== null,
    );
});

it('completes and persists the response when the stream finishes normally', function () {
    Event::fake([PortalAssistantMessageChunk::class]);

    $thread = PortalAssistantThread::factory()->create();

    $service = Mockery::mock(TestAiService::class)->makePartial();
    $service->shouldReceive('streamRaw')->andReturn(function () {
        yield new Text('Here is a complete answer.');

        yield new Finish();
    });
    app()->instance(TestAiService::class, $service);

    dispatch_sync(new SendMessage($thread, 'Hello'));

    $response = PortalAssistantMessage::query()->where('is_assistant', true)->first();

    expect($response)->not->toBeNull()
        ->and($response->content)->toBe('Here is a complete answer.');

    Event::assertDispatched(
        PortalAssistantMessageChunk::class,
        fn (PortalAssistantMessageChunk $event): bool => $event->isComplete === true,
    );
});
