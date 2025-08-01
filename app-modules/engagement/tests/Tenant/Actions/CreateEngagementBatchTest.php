<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Actions\CreateEngagementBatch;
use AidingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AidingApp\Engagement\Models\EngagementBatch;
use AidingApp\Engagement\Tests\Tenant\RequestFactories\CreateEngagementBatchRequestFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Testing\Fakes\PendingBatchFake;

use function Pest\Laravel\assertDatabaseCount;

it('will create an engagement batch', function () {
    Bus::fake();

    assertDatabaseCount(EngagementBatch::class, 0);

    $data = CreateEngagementBatchRequestFactory::new()->create();

    app(CreateEngagementBatch::class)->execute(new EngagementCreationData(
        user: $data['user'],
        recipient: Contact::factory()->count(10)->create(),
        channel: $data['channel'],
        subject: $data['subject'],
        body: $data['body'],
        scheduledAt: Carbon::parse($data['scheduledAt']),
    ));

    assertDatabaseCount(EngagementBatch::class, 1);

    expect(EngagementBatch::first())
        ->user->is($data['user'])->toBeTrue()
        ->channel->toBe($data['channel'])
        ->subject->toBe($data['subject'])
        ->body->toMatchArray($data['body'])
        ->scheduled_at->startOfSecond()->eq(Carbon::parse($data['scheduledAt'])->startOfSecond())->toBeTrue();

    Bus::assertBatched(fn (PendingBatchFake $batch): bool => $batch->jobs->count() === 10);
});
