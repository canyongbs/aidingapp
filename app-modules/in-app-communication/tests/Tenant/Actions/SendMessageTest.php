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

use AidingApp\InAppCommunication\Actions\SendMessage;
use AidingApp\InAppCommunication\Jobs\NotifyMessageParticipants;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use AidingApp\InAppCommunication\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\assertDatabaseCount;

it('creates a message in a conversation', function () {
    Event::fake();
    Queue::fake();

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

it('updates the author last_read_at timestamp', function () {
    Event::fake();
    Queue::fake();

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

it('dispatches NotifyMessageParticipants job', function () {
    Event::fake();
    Queue::fake();

    $conversation = Conversation::factory()->channel()->create();
    $author = User::factory()->create();

    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $author->getKey(),
    ]);

    app(SendMessage::class)(
        conversation: $conversation,
        author: $author,
        content: ['type' => 'doc', 'content' => []],
    );

    Queue::assertPushed(NotifyMessageParticipants::class);
});
