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

use AidingApp\InAppCommunication\Actions\UpdateParticipantRole;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('promotes a participant to manager', function () {
    Event::fake();

    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
        'is_manager' => false,
    ]);

    $participant = app(UpdateParticipantRole::class)(
        conversation: $conversation,
        user: $user,
        isManager: true,
    );

    expect($participant)
        ->not->toBeNull()
        ->is_manager->toBeTrue();
});

it('demotes a manager to regular participant when other managers exist', function () {
    Event::fake();

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

    $participant = app(UpdateParticipantRole::class)(
        conversation: $conversation,
        user: $manager1,
        isManager: false,
    );

    expect($participant)
        ->not->toBeNull()
        ->is_manager->toBeFalse();
});

it('returns null when participant does not exist', function () {
    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    $participant = app(UpdateParticipantRole::class)(
        conversation: $conversation,
        user: $user,
        isManager: true,
    );

    expect($participant)->toBeNull();
});

it('throws exception when demoting the last manager', function () {
    $conversation = Conversation::factory()->channel()->create();
    $manager = User::factory()->create();

    ConversationParticipant::factory()->manager()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $manager->getKey(),
    ]);

    app(UpdateParticipantRole::class)(
        conversation: $conversation,
        user: $manager,
        isManager: false,
    );
})->throws(InvalidArgumentException::class, 'Cannot remove the last manager from a channel.');

it('throws exception when updating role in direct message', function () {
    $conversation = Conversation::factory()->direct()->create();
    $user = User::factory()->create();

    app(UpdateParticipantRole::class)(
        conversation: $conversation,
        user: $user,
        isManager: true,
    );
})->throws(InvalidArgumentException::class, 'Cannot update participant roles in direct message conversations.');

it('allows setting manager to true when already a manager', function () {
    Event::fake();

    $conversation = Conversation::factory()->channel()->create();
    $manager = User::factory()->create();

    ConversationParticipant::factory()->manager()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $manager->getKey(),
    ]);

    $participant = app(UpdateParticipantRole::class)(
        conversation: $conversation,
        user: $manager,
        isManager: true,
    );

    expect($participant)
        ->not->toBeNull()
        ->is_manager->toBeTrue();
});
