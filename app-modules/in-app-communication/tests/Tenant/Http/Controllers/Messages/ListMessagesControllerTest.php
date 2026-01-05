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

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use AidingApp\InAppCommunication\Models\Message;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    Event::fake();
    Queue::fake();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->realtimeChat = true;
    $settings->save();
});

it('returns messages for a conversation', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    $message = Message::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $user->getKey(),
        'content' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Hello']]]]],
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.messages.index', $conversation))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $message->getKey());
});

it('returns messages in descending order by default', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    $olderMessage = Message::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $user->getKey(),
        'created_at' => now()->subHour(),
    ]);

    $newerMessage = Message::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $user->getKey(),
        'created_at' => now(),
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.messages.index', $conversation))
        ->assertOk()
        ->assertJsonPath('data.0.id', $newerMessage->getKey())
        ->assertJsonPath('data.1.id', $olderMessage->getKey());
});

it('limits messages by the limit parameter', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    Message::factory()->count(10)->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $user->getKey(),
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.messages.index', [$conversation, 'limit' => 5]))
        ->assertOk()
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('meta.has_more', true);
});

it('returns messages before a specific message', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    $message1 = Message::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $user->getKey(),
        'created_at' => now()->subHours(2),
    ]);

    $message2 = Message::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $user->getKey(),
        'created_at' => now()->subHour(),
    ]);

    $message3 = Message::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $user->getKey(),
        'created_at' => now(),
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.messages.index', [$conversation, 'before' => $message3->getKey()]))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $message2->getKey())
        ->assertJsonPath('data.1.id', $message1->getKey());
});

it('returns messages after a specific message', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    $message1 = Message::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $user->getKey(),
        'created_at' => now()->subHours(2),
    ]);

    $message2 = Message::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $user->getKey(),
        'created_at' => now()->subHour(),
    ]);

    $message3 = Message::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $user->getKey(),
        'created_at' => now(),
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.messages.index', [$conversation, 'after' => $message1->getKey()]))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $message2->getKey())
        ->assertJsonPath('data.1.id', $message3->getKey());
});

it('indicates when there are more messages available', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    Message::factory()->count(30)->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $user->getKey(),
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.messages.index', $conversation))
        ->assertOk()
        ->assertJsonPath('meta.has_more', true);
});

it('indicates when there are no more messages', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    Message::factory()->count(5)->create([
        'conversation_id' => $conversation->getKey(),
        'author_id' => $user->getKey(),
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.messages.index', $conversation))
        ->assertOk()
        ->assertJsonPath('meta.has_more', false);
});

it('validates request', function (array $data, array $expectedErrors) {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.messages.index', array_merge(['conversation' => $conversation->getKey()], $data)))
        ->assertStatus(422)
        ->assertJsonValidationErrors($expectedErrors);
})->with([
    'limit must be integer' => [
        ['limit' => 'not-integer'],
        ['limit' => 'The limit must be an integer.'],
    ],
    'limit minimum' => [
        ['limit' => 0],
        ['limit' => 'The limit must be at least 1.'],
    ],
    'limit maximum' => [
        ['limit' => 101],
        ['limit' => 'The limit may not be greater than 100.'],
    ],
    'before must be uuid' => [
        ['before' => 'not-uuid'],
        ['before' => 'The before must be a valid UUID.'],
    ],
    'after must be uuid' => [
        ['after' => 'not-uuid'],
        ['after' => 'The after must be a valid UUID.'],
    ],
]);

it('requires participant to view messages', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $conversation = Conversation::factory()->channel()->create(['is_private' => true]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.messages.index', $conversation))
        ->assertForbidden();
});

it('requires authentication', function () {
    $conversation = Conversation::factory()->channel()->create();

    getJson(route('in-app-communication.conversations.messages.index', $conversation))
        ->assertUnauthorized();
});

it('requires the realtime chat feature to be enabled', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->realtimeChat = false;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.conversations.messages.index', $conversation))
        ->assertForbidden();
});
