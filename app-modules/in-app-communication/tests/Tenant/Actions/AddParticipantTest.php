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

use AidingApp\InAppCommunication\Actions\AddParticipant;
use AidingApp\InAppCommunication\Events\ParticipantAdded;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\assertDatabaseCount;

it('adds a participant to a channel conversation', function () {
    Event::fake();

    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    assertDatabaseCount(ConversationParticipant::class, 0);

    $participant = app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
    );

    assertDatabaseCount(ConversationParticipant::class, 1);

    expect($participant)
        ->conversation_id->toBe($conversation->getKey())
        ->participant_id->toBe($user->getKey())
        ->is_manager->toBeFalse();
});

it('adds a participant as a manager when specified', function () {
    Event::fake();

    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    $participant = app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
        isManager: true,
    );

    expect($participant)
        ->is_manager->toBeTrue();
});

it('returns existing participant if already in conversation', function () {
    Event::fake();

    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    $firstParticipant = app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
    );

    assertDatabaseCount(ConversationParticipant::class, 1);

    $secondParticipant = app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
        isManager: true,
    );

    assertDatabaseCount(ConversationParticipant::class, 1);

    expect($secondParticipant->getKey())->toBe($firstParticipant->getKey());
    expect($secondParticipant->is_manager)->toBeFalse();
});

it('throws exception when adding participant to direct message', function () {
    $conversation = Conversation::factory()->direct()->create();
    $user = User::factory()->create();

    app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
    );
})->throws(InvalidArgumentException::class, 'Cannot add participants to direct message conversations.');

it('sets `last_activity_at` when adding a participant', function () {
    Event::fake();

    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    $participant = app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
    );

    expect($participant->last_activity_at)->not->toBeNull();
});

it('broadcasts `ParticipantAdded` event when adding participant to public channel', function () {
    Event::fake([ParticipantAdded::class]);

    $conversation = Conversation::factory()->channel()->create([
        'is_private' => false,
    ]);
    $user = User::factory()->create();

    app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
    );

    Event::assertDispatched(ParticipantAdded::class, function ($event) use ($conversation, $user) {
        return $event->participant->conversation_id === $conversation->getKey()
            && $event->participant->participant_id === $user->getKey();
    });
});

it('broadcasts `ParticipantAdded` event when adding participant to private channel', function () {
    Event::fake([ParticipantAdded::class]);

    $conversation = Conversation::factory()->channel()->create([
        'is_private' => true,
    ]);
    $user = User::factory()->create();

    app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
    );

    Event::assertDispatched(ParticipantAdded::class, function ($event) use ($conversation, $user) {
        return $event->participant->conversation_id === $conversation->getKey()
            && $event->participant->participant_id === $user->getKey();
    });
});

it('does not broadcast `ParticipantAdded` event when participant already exists', function () {
    Event::fake();

    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    // Add participant first
    app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
    );

    Event::assertDispatchedTimes(ParticipantAdded::class, 1);

    // Try to add again
    app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
    );

    // Should still only be 1 dispatch (not 2)
    Event::assertDispatchedTimes(ParticipantAdded::class, 1);
});

it('broadcasts `ParticipantAdded` event to both conversation and user channels', function () {
    Event::fake();

    $conversation = Conversation::factory()->channel()->create([
        'is_private' => true,
        'name' => 'Test Private Channel',
    ]);
    $user = User::factory()->create();

    $participant = app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
    );

    $event = new ParticipantAdded($participant);
    $channels = $event->broadcastOn();

    expect($channels)->toHaveCount(2);
    expect($channels[0]->name)->toBe("private-conversation.{$conversation->getKey()}");
    expect($channels[1]->name)->toBe("private-user.{$user->getKey()}");
});

it('includes conversation data with display_name in `ParticipantAdded` event', function () {
    Event::fake();

    $conversation = Conversation::factory()->channel()->create([
        'is_private' => true,
        'name' => 'My Private Channel',
    ]);
    $user = User::factory()->create();

    $participant = app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
    );

    $event = new ParticipantAdded($participant);
    $payload = $event->broadcastWith();

    expect($payload)
        ->toHaveKey('conversation')
        ->toHaveKey('participant_id')
        ->and($payload['participant_id'])->toBe($user->getKey())
        ->and($payload['conversation']['id'])->toBe($conversation->getKey())
        ->and($payload['conversation']['display_name'])->toBe('My Private Channel')
        ->and($payload['conversation']['is_private'])->toBeTrue();
});
