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
    $user = User::factory()->licensed(LicenseType::cases())->create();

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

it('sorts conversations by last message date', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $olderConversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $olderConversation->getKey(),
        'participant_id' => $user->getKey(),
        'created_at' => now()->subWeek(),
    ]);
    Message::factory()->create([
        'conversation_id' => $olderConversation->getKey(),
        'author_id' => $user->getKey(),
        'created_at' => now()->subDay(),
    ]);

    $newerConversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $newerConversation->getKey(),
        'participant_id' => $user->getKey(),
        'created_at' => now()->subWeek(),
    ]);
    Message::factory()->create([
        'conversation_id' => $newerConversation->getKey(),
        'author_id' => $user->getKey(),
        'created_at' => now(),
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.index'))
        ->assertOk()
        ->assertJsonPath('data.0.id', $newerConversation->getKey())
        ->assertJsonPath('data.1.id', $olderConversation->getKey());
});

it('sorts conversations without messages by participant join date', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $olderJoinConversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $olderJoinConversation->getKey(),
        'participant_id' => $user->getKey(),
        'created_at' => now()->subDay(),
    ]);

    $newerJoinConversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $newerJoinConversation->getKey(),
        'participant_id' => $user->getKey(),
        'created_at' => now(),
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.index'))
        ->assertOk()
        ->assertJsonPath('data.0.id', $newerJoinConversation->getKey())
        ->assertJsonPath('data.1.id', $olderJoinConversation->getKey());
});

it('uses the more recent of message date or join date for sorting', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    // Conversation with old message but user joined recently
    $recentJoinConversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $recentJoinConversation->getKey(),
        'participant_id' => $user->getKey(),
        'created_at' => now(),
    ]);
    Message::factory()->create([
        'conversation_id' => $recentJoinConversation->getKey(),
        'author_id' => $user->getKey(),
        'created_at' => now()->subWeek(),
    ]);

    // Conversation with recent message but user joined long ago
    $recentMessageConversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $recentMessageConversation->getKey(),
        'participant_id' => $user->getKey(),
        'created_at' => now()->subMonth(),
    ]);
    Message::factory()->create([
        'conversation_id' => $recentMessageConversation->getKey(),
        'author_id' => $user->getKey(),
        'created_at' => now()->subHour(),
    ]);

    // The conversation with the recent join should appear first (now > 1 hour ago)
    actingAs($user)
        ->getJson(route('in-app-communication.conversations.index'))
        ->assertOk()
        ->assertJsonPath('data.0.id', $recentJoinConversation->getKey())
        ->assertJsonPath('data.1.id', $recentMessageConversation->getKey());
});

it('requires authentication', function () {
    getJson(route('in-app-communication.conversations.index'))
        ->assertUnauthorized();
});

it('requires the realtime chat feature to be enabled', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->realtimeChat = false;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.index'))
        ->assertForbidden();
});
