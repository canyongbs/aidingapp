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

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use AidingApp\InAppCommunication\Models\Message;
use AidingApp\InAppCommunication\Models\Scopes\WithUnreadCount;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
});

it('returns 0 when no messages exist', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    $result = Conversation::query()
        ->tap(new WithUnreadCount($user))
        ->first();

    expect($result->unread_count)->toBe(0);
});

it('excludes messages sent by the current user', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $otherUser = User::factory()->licensed(LicenseType::cases())->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $user->getKey(),
        'author_type' => $user->getMorphClass(),
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $otherUser->getKey(),
        'author_type' => $otherUser->getMorphClass(),
    ]);

    $result = Conversation::query()
        ->tap(new WithUnreadCount($user))
        ->first();

    expect($result->unread_count)->toBe(1);
});

it('excludes messages created before last_read_at', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $otherUser = User::factory()->licensed(LicenseType::cases())->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
        'last_read_at' => now(),
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $otherUser->getKey(),
        'author_type' => $otherUser->getMorphClass(),
        'created_at' => now()->subHour(),
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $otherUser->getKey(),
        'author_type' => $otherUser->getMorphClass(),
        'created_at' => now()->addHour(),
    ]);

    $result = Conversation::query()
        ->tap(new WithUnreadCount($user))
        ->first();

    expect($result->unread_count)->toBe(1);
});

it('counts all messages from others when last_read_at is null', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $otherUser = User::factory()->licensed(LicenseType::cases())->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
        'last_read_at' => null,
    ]);

    Message::factory()->count(3)->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $otherUser->getKey(),
        'author_type' => $otherUser->getMorphClass(),
    ]);

    $result = Conversation::query()
        ->tap(new WithUnreadCount($user))
        ->first();

    expect($result->unread_count)->toBe(3);
});

it('works correctly across multiple conversations', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $otherUser = User::factory()->licensed(LicenseType::cases())->create();

    $conversation1 = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation1->getKey(),
        'participant_id' => $user->getKey(),
        'last_read_at' => null,
    ]);
    Message::factory()->count(2)->create([
        'conversation_id' => $conversation1->getKey(),
        'author_id' => $otherUser->getKey(),
        'author_type' => $otherUser->getMorphClass(),
    ]);

    $conversation2 = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation2->getKey(),
        'participant_id' => $user->getKey(),
        'last_read_at' => now(),
    ]);
    Message::factory()->create([
        'conversation_id' => $conversation2->getKey(),
        'author_id' => $otherUser->getKey(),
        'author_type' => $otherUser->getMorphClass(),
        'created_at' => now()->addHour(),
    ]);

    $results = Conversation::query()
        ->tap(new WithUnreadCount($user))
        ->get()
        ->keyBy('id');

    expect($results[$conversation1->getKey()]->unread_count)->toBe(2);
    expect($results[$conversation2->getKey()]->unread_count)->toBe(1);
});
