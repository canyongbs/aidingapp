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

use AidingApp\InAppCommunication\Actions\UpdateConversation;
use AidingApp\InAppCommunication\Events\ConversationUpdated;
use AidingApp\InAppCommunication\Models\Conversation;
use Illuminate\Support\Facades\Event;

it('updates the conversation name', function () {
    $conversation = Conversation::factory()->channel()->create([
        'name' => 'Original Name',
    ]);

    $result = app(UpdateConversation::class)(
        conversation: $conversation,
        name: 'New Name',
    );

    expect($result->name)->toBe('New Name');
    expect($conversation->fresh()->name)->toBe('New Name');
});

it('updates the conversation privacy setting', function () {
    $conversation = Conversation::factory()->channel()->create([
        'is_private' => true,
    ]);

    $result = app(UpdateConversation::class)(
        conversation: $conversation,
        isPrivate: false,
    );

    expect($result->is_private)->toBeFalse();
    expect($conversation->fresh()->is_private)->toBeFalse();
});

it('updates both name and privacy setting', function () {
    $conversation = Conversation::factory()->channel()->create([
        'name' => 'Original Name',
        'is_private' => true,
    ]);

    $result = app(UpdateConversation::class)(
        conversation: $conversation,
        name: 'New Name',
        isPrivate: false,
    );

    expect($result->name)->toBe('New Name');
    expect($result->is_private)->toBeFalse();
});

it('does not save when no changes are made', function () {
    $conversation = Conversation::factory()->channel()->create([
        'name' => 'Original Name',
    ]);

    $originalUpdatedAt = $conversation->updated_at;

    // Small delay to ensure timestamps would differ
    usleep(10000);

    app(UpdateConversation::class)(
        conversation: $conversation,
    );

    expect($conversation->fresh()->updated_at->equalTo($originalUpdatedAt))->toBeTrue();
});

it('broadcasts `ConversationUpdated` event when name is updated', function () {
    Event::fake([ConversationUpdated::class]);

    $conversation = Conversation::factory()->channel()->create([
        'name' => 'Original Name',
    ]);

    app(UpdateConversation::class)(
        conversation: $conversation,
        name: 'New Name',
    );

    Event::assertDispatched(ConversationUpdated::class, function ($event) use ($conversation) {
        return $event->conversation->is($conversation) && $event->conversation->name === 'New Name';
    });
});

it('broadcasts `ConversationUpdated` event when privacy is updated', function () {
    Event::fake([ConversationUpdated::class]);

    $conversation = Conversation::factory()->channel()->create([
        'is_private' => true,
    ]);

    app(UpdateConversation::class)(
        conversation: $conversation,
        isPrivate: false,
    );

    Event::assertDispatched(ConversationUpdated::class, function ($event) use ($conversation) {
        return $event->conversation->is($conversation) && $event->conversation->is_private === false;
    });
});

it('does not broadcast `ConversationUpdated` event when no changes are made', function () {
    Event::fake([ConversationUpdated::class]);

    $conversation = Conversation::factory()->channel()->create();

    app(UpdateConversation::class)(
        conversation: $conversation,
    );

    Event::assertNotDispatched(ConversationUpdated::class);
});
