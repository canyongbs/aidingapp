<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\InAppCommunication\Enums\ConversationEphemeralPeriod;
use AidingApp\InAppCommunication\Events\MessagesPruned;
use AidingApp\InAppCommunication\Events\UnreadCountUpdated;
use AidingApp\InAppCommunication\Jobs\PruneEphemeralMessages;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use AidingApp\InAppCommunication\Models\Message;
use App\Features\ConfidentialChannelsFeature;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;

it('deletes messages past the ephemeral period and keeps the rest', function () {
    $conversation = Conversation::factory()
        ->confidential(ConversationEphemeralPeriod::OneHour)
        ->create();

    $expired = Message::factory()->for($conversation)->create([
        'created_at' => now()->subHours(2),
    ]);

    $recent = Message::factory()->for($conversation)->create([
        'created_at' => now()->subMinutes(5),
    ]);

    Event::fake();

    assertModelExists($expired);

    (new PruneEphemeralMessages())->handle();

    assertModelMissing($expired);
    assertModelExists($recent);
});

it('does not delete messages in a confidential conversation without an ephemeral period', function () {
    $conversation = Conversation::factory()
        ->confidential(null)
        ->create();

    $message = Message::factory()->for($conversation)->create([
        'created_at' => now()->subYears(2),
    ]);

    Event::fake();

    (new PruneEphemeralMessages())->handle();

    assertModelExists($message);
});

it('does not delete messages in a conversation that is not confidential', function () {
    $conversation = Conversation::factory()->channel()->create([
        'ephemeral_period' => ConversationEphemeralPeriod::OneMinute,
    ]);

    $message = Message::factory()->for($conversation)->create([
        'created_at' => now()->subYears(2),
    ]);

    Event::fake();

    (new PruneEphemeralMessages())->handle();

    assertModelExists($message);
});

it('only deletes messages belonging to the expired conversation', function () {
    $expiring = Conversation::factory()
        ->confidential(ConversationEphemeralPeriod::OneMinute)
        ->create();

    $retaining = Conversation::factory()
        ->confidential(ConversationEphemeralPeriod::OneYear)
        ->create();

    $deleted = Message::factory()->for($expiring)->create([
        'created_at' => now()->subHour(),
    ]);

    $kept = Message::factory()->for($retaining)->create([
        'created_at' => now()->subHour(),
    ]);

    Event::fake();

    (new PruneEphemeralMessages())->handle();

    assertModelMissing($deleted);
    assertModelExists($kept);
});

it('broadcasts `MessagesPruned` for a conversation it pruned', function () {
    $conversation = Conversation::factory()
        ->confidential(ConversationEphemeralPeriod::OneHour)
        ->create();

    Message::factory()->for($conversation)->create([
        'created_at' => now()->subHours(2),
    ]);

    Event::fake();

    (new PruneEphemeralMessages())->handle();

    Event::assertDispatched(
        MessagesPruned::class,
        fn (MessagesPruned $event) => $event->conversation->is($conversation),
    );
});

it('does not broadcast `MessagesPruned` when nothing was pruned', function () {
    $conversation = Conversation::factory()
        ->confidential(ConversationEphemeralPeriod::OneHour)
        ->create();

    Message::factory()->for($conversation)->create([
        'created_at' => now()->subMinutes(5),
    ]);

    Event::fake();

    (new PruneEphemeralMessages())->handle();

    Event::assertNotDispatched(MessagesPruned::class);
});

it('lowers an unread count that can no longer be reached', function () {
    $conversation = Conversation::factory()
        ->confidential(ConversationEphemeralPeriod::OneHour)
        ->create();

    Message::factory()->for($conversation)->create([
        'created_at' => now()->subHours(2),
    ]);

    $participant = ConversationParticipant::factory()
        ->for($conversation)
        ->create([
            'last_read_at' => null,
            'unread_count' => 1,
        ]);

    Event::fake();

    (new PruneEphemeralMessages())->handle();

    expect($participant->refresh()->unread_count)->toBe(0);

    Event::assertDispatched(
        UnreadCountUpdated::class,
        fn (UnreadCountUpdated $event) => $event->conversationId === $conversation->getKey()
            && $event->unreadCount === 0,
    );
});

it('leaves an unread count alone when the remaining messages still support it', function () {
    $conversation = Conversation::factory()
        ->confidential(ConversationEphemeralPeriod::OneHour)
        ->create();

    Message::factory()->for($conversation)->create([
        'created_at' => now()->subHours(2),
    ]);

    Message::factory()->for($conversation)->create([
        'created_at' => now()->subMinutes(5),
    ]);

    $participant = ConversationParticipant::factory()
        ->for($conversation)
        ->create([
            'last_read_at' => null,
            'unread_count' => 1,
        ]);

    Event::fake();

    (new PruneEphemeralMessages())->handle();

    expect($participant->refresh()->unread_count)->toBe(1);
});

it('does nothing when the confidential channels feature is inactive', function () {
    $conversation = Conversation::factory()
        ->confidential(ConversationEphemeralPeriod::OneMinute)
        ->create();

    $message = Message::factory()->for($conversation)->create([
        'created_at' => now()->subYears(2),
    ]);

    ConfidentialChannelsFeature::deactivate();

    Event::fake();

    (new PruneEphemeralMessages())->handle();

    assertModelExists($message);
    Event::assertNotDispatched(MessagesPruned::class);
});
