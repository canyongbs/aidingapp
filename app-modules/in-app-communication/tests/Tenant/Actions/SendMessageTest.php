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

use AidingApp\InAppCommunication\Actions\SendMessage;
use AidingApp\InAppCommunication\Enums\ConversationNotificationPreference;
use AidingApp\InAppCommunication\Events\MessageSent;
use AidingApp\InAppCommunication\Events\UnreadCountUpdated;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use AidingApp\InAppCommunication\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\assertDatabaseCount;

beforeEach(fn () => Event::fake());

it('creates a message in a conversation', function () {
    $conversation = Conversation::factory()->channel()->create();
    $author = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $author->getKey(),
    ]);

    assertDatabaseCount(Message::class, 0);

    $content = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello, World!',
                    ],
                ],
            ],
        ],
    ];

    $message = app(SendMessage::class)(
        conversation: $conversation,
        author: $author,
        content: $content,
    );

    assertDatabaseCount(Message::class, 1);

    expect($message)
        ->conversation_id->toBe($conversation->getKey())
        ->author_id->toBe($author->getKey())
        ->content->toBe($content);
});

it('updates the author `last_read_at` timestamp', function () {
    $conversation = Conversation::factory()->channel()->create();
    $author = User::factory()->create();

    $participant = ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $author->getKey(),
        'last_read_at' => null,
    ]);

    expect($participant->last_read_at)->toBeNull();

    app(SendMessage::class)(
        conversation: $conversation,
        author: $author,
        content: ['type' => 'doc', 'content' => []],
    );

    $participant->refresh();

    expect($participant->last_read_at)->not->toBeNull();
});

it('updates the author `last_activity_at` timestamp', function () {
    $conversation = Conversation::factory()->channel()->create();
    $author = User::factory()->create();

    $participant = ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $author->getKey(),
        'last_activity_at' => now()->subDay(),
    ]);

    $oldActivityAt = $participant->last_activity_at;

    app(SendMessage::class)(
        conversation: $conversation,
        author: $author,
        content: ['type' => 'doc', 'content' => []],
    );

    $participant->refresh();

    expect($participant->last_activity_at->isAfter($oldActivityAt))->toBeTrue();
});

it('updates `last_activity_at` for all other participants', function () {
    $conversation = Conversation::factory()->channel()->create();
    $author = User::factory()->create();
    $otherUser = User::factory()->create();

    $authorParticipant = ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $author->getKey(),
        'last_activity_at' => now()->subDay(),
    ]);

    $otherParticipant = ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $otherUser->getKey(),
        'last_activity_at' => now()->subDay(),
    ]);

    $oldActivityAt = $otherParticipant->last_activity_at;

    app(SendMessage::class)(
        conversation: $conversation,
        author: $author,
        content: ['type' => 'doc', 'content' => []],
    );

    $otherParticipant->refresh();

    expect($otherParticipant->last_activity_at->isAfter($oldActivityAt))->toBeTrue();
});

it('increments `unread_count` for participants with All notification preference', function () {
    $conversation = Conversation::factory()->channel()->create();
    $author = User::factory()->create();
    $otherUser = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $author->getKey(),
        'unread_count' => 0,
    ]);

    $otherParticipant = ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $otherUser->getKey(),
        'notification_preference' => ConversationNotificationPreference::All,
        'unread_count' => 0,
    ]);

    app(SendMessage::class)(
        conversation: $conversation,
        author: $author,
        content: ['type' => 'doc', 'content' => []],
    );

    $otherParticipant->refresh();

    expect($otherParticipant->unread_count)->toBe(1);
});

it('does not increment `unread_count` for the message author', function () {
    $conversation = Conversation::factory()->channel()->create();
    $author = User::factory()->create();

    $authorParticipant = ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $author->getKey(),
        'notification_preference' => ConversationNotificationPreference::All,
        'unread_count' => 0,
    ]);

    app(SendMessage::class)(
        conversation: $conversation,
        author: $author,
        content: ['type' => 'doc', 'content' => []],
    );

    $authorParticipant->refresh();

    expect($authorParticipant->unread_count)->toBe(0);
});

it('does not increment `unread_count` for participants with None notification preference', function () {
    $conversation = Conversation::factory()->channel()->create();
    $author = User::factory()->create();
    $otherUser = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $author->getKey(),
    ]);

    $otherParticipant = ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $otherUser->getKey(),
        'notification_preference' => ConversationNotificationPreference::None,
        'unread_count' => 0,
    ]);

    app(SendMessage::class)(
        conversation: $conversation,
        author: $author,
        content: ['type' => 'doc', 'content' => []],
    );

    $otherParticipant->refresh();

    expect($otherParticipant->unread_count)->toBe(0);
});

it('increments `unread_count` for participants with Mentions preference only when mentioned', function () {
    $conversation = Conversation::factory()->channel()->create();
    $author = User::factory()->create();
    $mentionedUser = User::factory()->create();
    $notMentionedUser = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $author->getKey(),
    ]);

    $mentionedParticipant = ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $mentionedUser->getKey(),
        'notification_preference' => ConversationNotificationPreference::Mentions,
        'unread_count' => 0,
    ]);

    $notMentionedParticipant = ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $notMentionedUser->getKey(),
        'notification_preference' => ConversationNotificationPreference::Mentions,
        'unread_count' => 0,
    ]);

    $contentWithMention = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'mention',
                        'attrs' => [
                            'id' => $mentionedUser->getKey(),
                            'label' => $mentionedUser->name,
                        ],
                    ],
                ],
            ],
        ],
    ];

    app(SendMessage::class)(
        conversation: $conversation,
        author: $author,
        content: $contentWithMention,
    );

    $mentionedParticipant->refresh();
    $notMentionedParticipant->refresh();

    expect($mentionedParticipant->unread_count)->toBe(1);
    expect($notMentionedParticipant->unread_count)->toBe(0);
});

it('atomically increments `unread_count` for multiple messages', function () {
    $conversation = Conversation::factory()->channel()->create();
    $author = User::factory()->create();
    $otherUser = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $author->getKey(),
    ]);

    $otherParticipant = ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $otherUser->getKey(),
        'notification_preference' => ConversationNotificationPreference::All,
        'unread_count' => 5,
    ]);

    app(SendMessage::class)(
        conversation: $conversation,
        author: $author,
        content: ['type' => 'doc', 'content' => []],
    );

    $otherParticipant->refresh();

    expect($otherParticipant->unread_count)->toBe(6);
});

it('broadcasts `UnreadCountUpdated` event to affected participants', function () {
    $conversation = Conversation::factory()->channel()->create();
    $author = User::factory()->create();
    $otherUser = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $author->getKey(),
    ]);

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $otherUser->getKey(),
        'notification_preference' => ConversationNotificationPreference::All,
    ]);

    app(SendMessage::class)(
        conversation: $conversation,
        author: $author,
        content: ['type' => 'doc', 'content' => []],
    );

    Event::assertDispatched(UnreadCountUpdated::class, function ($event) use ($conversation, $otherUser) {
        return $event->userId === $otherUser->getKey()
            && $event->conversationId === $conversation->getKey()
            && $event->unreadCount === 1;
    });
});

it('does not broadcast `UnreadCountUpdated` event to participants with None preference', function () {
    $conversation = Conversation::factory()->channel()->create();
    $author = User::factory()->create();
    $otherUser = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $author->getKey(),
    ]);

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $otherUser->getKey(),
        'notification_preference' => ConversationNotificationPreference::None,
    ]);

    app(SendMessage::class)(
        conversation: $conversation,
        author: $author,
        content: ['type' => 'doc', 'content' => []],
    );

    Event::assertNotDispatched(UnreadCountUpdated::class, function ($event) use ($otherUser) {
        return $event->userId === $otherUser->getKey();
    });
});

it('broadcasts `MessageSent` event to user channels for all participants except author', function () {
    $conversation = Conversation::factory()->channel()->create();
    $author = User::factory()->create();
    $participant1 = User::factory()->create();
    $participant2 = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $author->getKey(),
    ]);

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $participant1->getKey(),
    ]);

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $participant2->getKey(),
    ]);

    $message = app(SendMessage::class)(
        conversation: $conversation,
        author: $author,
        content: ['type' => 'doc', 'content' => []],
    );

    $event = new MessageSent($message);
    $channels = $event->broadcastOn();

    // Should broadcast to participant1 and participant2, but NOT author
    expect($channels)->toHaveCount(2);

    $channelNames = collect($channels)->map(fn ($channel) => $channel->name)->all();
    expect($channelNames)->toContain("private-user.{$participant1->getKey()}");
    expect($channelNames)->toContain("private-user.{$participant2->getKey()}");
    expect($channelNames)->not->toContain("private-user.{$author->getKey()}");
});
