<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AidingApp\InAppCommunication\Actions\RemoveParticipant;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(fn () => Event::fake());

it('removes a participant from a channel conversation', function () {
    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    assertDatabaseCount(ConversationParticipant::class, 1);

    $result = app(RemoveParticipant::class)(
        conversation: $conversation,
        user: $user,
    );

    expect($result)->toBeTrue();
    assertDatabaseCount(ConversationParticipant::class, 0);
});

it('returns false when participant does not exist', function () {
    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    $result = app(RemoveParticipant::class)(
        conversation: $conversation,
        user: $user,
    );

    expect($result)->toBeFalse();
});

it('throws exception when removing participant from direct message', function () {
    $conversation = Conversation::factory()->direct()->create();
    $user = User::factory()->create();

    app(RemoveParticipant::class)(
        conversation: $conversation,
        user: $user,
    );
})->throws(InvalidArgumentException::class, 'Cannot remove participants from direct message conversations.');

it('throws exception when removing the last manager from a public channel', function () {
    $conversation = Conversation::factory()->channel()->create([
        'is_private' => false,
    ]);
    $manager = User::factory()->create();

    ConversationParticipant::factory()->manager()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $manager->getKey(),
    ]);

    app(RemoveParticipant::class)(
        conversation: $conversation,
        user: $manager,
    );
})->throws(InvalidArgumentException::class, 'Cannot remove the last manager from a channel.');

it('allows removing a manager when other managers exist', function () {
    $conversation = Conversation::factory()->channel()->create();
    $manager1 = User::factory()->create();
    $manager2 = User::factory()->create();

    ConversationParticipant::factory()->manager()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $manager1->getKey(),
    ]);

    ConversationParticipant::factory()->manager()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $manager2->getKey(),
    ]);

    assertDatabaseCount(ConversationParticipant::class, 2);

    $result = app(RemoveParticipant::class)(
        conversation: $conversation,
        user: $manager1,
    );

    expect($result)->toBeTrue();
    assertDatabaseCount(ConversationParticipant::class, 1);

    assertDatabaseMissing(ConversationParticipant::class, [
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $manager1->getKey(),
    ]);
});

it('allows removing non-manager participants regardless of manager count', function () {
    $conversation = Conversation::factory()->channel()->create();
    $manager = User::factory()->create();
    $regularUser = User::factory()->create();

    ConversationParticipant::factory()->manager()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $manager->getKey(),
    ]);

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $regularUser->getKey(),
    ]);

    assertDatabaseCount(ConversationParticipant::class, 2);

    $result = app(RemoveParticipant::class)(
        conversation: $conversation,
        user: $regularUser,
    );

    expect($result)->toBeTrue();
    assertDatabaseCount(ConversationParticipant::class, 1);
});

it('allows last manager to leave a private channel when they are the only participant', function () {
    $conversation = Conversation::factory()->channel()->create([
        'is_private' => true,
    ]);
    $manager = User::factory()->create();

    ConversationParticipant::factory()->manager()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $manager->getKey(),
    ]);

    assertDatabaseCount(ConversationParticipant::class, 1);

    $result = app(RemoveParticipant::class)(
        conversation: $conversation,
        user: $manager,
    );

    expect($result)->toBeTrue();
    assertDatabaseCount(ConversationParticipant::class, 0);
});

it('throws exception when last manager tries to leave a private channel with other participants', function () {
    $conversation = Conversation::factory()->channel()->create([
        'is_private' => true,
    ]);
    $manager = User::factory()->create();
    $regularUser = User::factory()->create();

    ConversationParticipant::factory()->manager()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $manager->getKey(),
    ]);

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $regularUser->getKey(),
    ]);

    app(RemoveParticipant::class)(
        conversation: $conversation,
        user: $manager,
    );
})->throws(InvalidArgumentException::class, 'Cannot remove the last manager from a channel.');

it('throws exception when last manager tries to leave a public channel even as only participant', function () {
    $conversation = Conversation::factory()->channel()->create([
        'is_private' => false,
    ]);
    $manager = User::factory()->create();

    ConversationParticipant::factory()->manager()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $manager->getKey(),
    ]);

    app(RemoveParticipant::class)(
        conversation: $conversation,
        user: $manager,
    );
})->throws(InvalidArgumentException::class, 'Cannot remove the last manager from a channel.');
