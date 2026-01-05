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

use AidingApp\InAppCommunication\Actions\UpdateParticipantSettings;
use AidingApp\InAppCommunication\Enums\ConversationNotificationPreference;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use App\Models\User;

it('updates `is_pinned` setting', function () {
    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
        'is_pinned' => false,
    ]);

    $participant = app(UpdateParticipantSettings::class)(
        conversation: $conversation,
        user: $user,
        isPinned: true,
    );

    expect($participant)
        ->not->toBeNull()
        ->is_pinned->toBeTrue();
});

it('updates `notification_preference` setting', function () {
    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
        'notification_preference' => ConversationNotificationPreference::All,
    ]);

    $participant = app(UpdateParticipantSettings::class)(
        conversation: $conversation,
        user: $user,
        notificationPreference: ConversationNotificationPreference::Mentions,
    );

    expect($participant)
        ->not->toBeNull()
        ->notification_preference->toBe(ConversationNotificationPreference::Mentions);
});

it('updates both settings at once', function () {
    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
        'is_pinned' => false,
        'notification_preference' => ConversationNotificationPreference::All,
    ]);

    $participant = app(UpdateParticipantSettings::class)(
        conversation: $conversation,
        user: $user,
        isPinned: true,
        notificationPreference: ConversationNotificationPreference::None,
    );

    expect($participant)
        ->not->toBeNull()
        ->is_pinned->toBeTrue()
        ->notification_preference->toBe(ConversationNotificationPreference::None);
});

it('returns null when participant does not exist', function () {
    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    $participant = app(UpdateParticipantSettings::class)(
        conversation: $conversation,
        user: $user,
        isPinned: true,
    );

    expect($participant)->toBeNull();
});

it('does not update when no settings are provided', function () {
    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    $originalParticipant = ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
        'is_pinned' => true,
        'notification_preference' => ConversationNotificationPreference::Mentions,
    ]);

    $participant = app(UpdateParticipantSettings::class)(
        conversation: $conversation,
        user: $user,
    );

    expect($participant)
        ->not->toBeNull()
        ->is_pinned->toBeTrue()
        ->notification_preference->toBe(ConversationNotificationPreference::Mentions);
});

it('can unpin a conversation', function () {
    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
        'is_pinned' => true,
    ]);

    $participant = app(UpdateParticipantSettings::class)(
        conversation: $conversation,
        user: $user,
        isPinned: false,
    );

    expect($participant)
        ->not->toBeNull()
        ->is_pinned->toBeFalse();
});
