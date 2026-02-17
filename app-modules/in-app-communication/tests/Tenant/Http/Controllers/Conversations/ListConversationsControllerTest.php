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

use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use AidingApp\InAppCommunication\Models\Message;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    Event::fake();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->realtimeChat = true;
    $settings->save();
});

it('returns conversations for the authenticated user', function () {
    $user = User::factory()->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    $otherConversation = Conversation::factory()->channel()->create();

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $conversation->getKey());
});

it('sorts conversations by last activity date', function () {
    $user = User::factory()->create();

    $olderConversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $olderConversation->getKey(),
        'participant_id' => $user->getKey(),
        'last_activity_at' => now()->subDay(),
    ]);

    $newerConversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $newerConversation->getKey(),
        'participant_id' => $user->getKey(),
        'last_activity_at' => now(),
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.index'))
        ->assertOk()
        ->assertJsonPath('data.0.id', $newerConversation->getKey())
        ->assertJsonPath('data.1.id', $olderConversation->getKey());
});

it('sorts newly joined conversations at the top', function () {
    $user = User::factory()->create();

    $olderJoinConversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $olderJoinConversation->getKey(),
        'participant_id' => $user->getKey(),
        'last_activity_at' => now()->subDay(),
    ]);

    $newerJoinConversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $newerJoinConversation->getKey(),
        'participant_id' => $user->getKey(),
        'last_activity_at' => now(),
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.index'))
        ->assertOk()
        ->assertJsonPath('data.0.id', $newerJoinConversation->getKey())
        ->assertJsonPath('data.1.id', $olderJoinConversation->getKey());
});

it('requires authentication', function () {
    getJson(route('in-app-communication.conversations.index'))
        ->assertUnauthorized();
});

it('requires the realtime chat feature to be enabled', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->realtimeChat = false;
    $settings->save();

    $user = User::factory()->create();

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.index'))
        ->assertForbidden();
});

it('returns pagination metadata', function () {
    $user = User::factory()->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data',
            'next_cursor',
            'has_more',
        ])
        ->assertJsonPath('has_more', false);
});

it('respects the limit parameter', function () {
    $user = User::factory()->create();

    $conversations = Conversation::factory()->channel()->count(5)->create();

    foreach ($conversations as $conversation) {
        ConversationParticipant::factory()->create([
            'conversation_id' => $conversation->getKey(),
            'participant_id' => $user->getKey(),
        ]);
    }

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.index', ['limit' => 2]))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('has_more', true);
});

it('can paginate through conversations using cursor', function () {
    $user = User::factory()->create();

    for ($i = 0; $i < 5; $i++) {
        $conversation = Conversation::factory()->channel()->create();
        ConversationParticipant::factory()->create([
            'conversation_id' => $conversation->getKey(),
            'participant_id' => $user->getKey(),
            'last_activity_at' => now()->subMinutes($i),
        ]);
    }

    $response = actingAs($user)
        ->getJson(route('in-app-communication.conversations.index', ['limit' => 2]))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('has_more', true);

    $nextCursor = $response->json('next_cursor');
    expect($nextCursor)->not->toBeNull();

    $response = actingAs($user)
        ->getJson(route('in-app-communication.conversations.index', ['limit' => 2, 'cursor' => $nextCursor]))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('has_more', true);

    $nextCursor = $response->json('next_cursor');

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.index', ['limit' => 2, 'cursor' => $nextCursor]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('has_more', false)
        ->assertJsonPath('next_cursor', null);
});

it('returns correct conversation data structure', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $conversation = Conversation::factory()->channel()->create([
        'name' => 'Test Channel',
    ]);

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
        'is_pinned' => false,
    ]);

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $otherUser->getKey(),
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $otherUser->getKey(),
        'author_type' => $otherUser->getMorphClass(),
        'content' => 'Hello world',
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'name',
                    'is_private',
                    'is_pinned',
                    'notification_preference',
                    'unread_count',
                    'last_read_at',
                    'last_message' => [
                        'id',
                        'content',
                        'author_id',
                        'author_name',
                        'created_at',
                    ],
                    'participant_count',
                    'created_at',
                ],
            ],
        ])
        ->assertJsonPath('data.0.name', 'Test Channel')
        ->assertJsonPath('data.0.is_pinned', false)
        ->assertJsonPath('data.0.participant_count', 2);
});
