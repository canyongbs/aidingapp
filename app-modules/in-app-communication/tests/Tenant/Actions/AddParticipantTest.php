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

use AidingApp\InAppCommunication\Actions\AddParticipant;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use App\Models\User;

use function Pest\Laravel\assertDatabaseCount;

it('adds a participant to a channel conversation', function () {
    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    assertDatabaseCount(ConversationParticipant::class, 0);

    $participant = app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
    );

    assertDatabaseCount(ConversationParticipant::class, 1);

    expect($participant)
        ->conversation_id->toBe($conversation->getKey())
        ->participant_id->toBe($user->getKey())
        ->is_manager->toBeFalse();
});

it('adds a participant as a manager when specified', function () {
    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    $participant = app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
        isManager: true,
    );

    expect($participant)
        ->is_manager->toBeTrue();
});

it('returns existing participant if already in conversation', function () {
    $conversation = Conversation::factory()->channel()->create();
    $user = User::factory()->create();

    $firstParticipant = app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
    );

    assertDatabaseCount(ConversationParticipant::class, 1);

    $secondParticipant = app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
        isManager: true,
    );

    assertDatabaseCount(ConversationParticipant::class, 1);

    expect($secondParticipant->getKey())->toBe($firstParticipant->getKey());
    expect($secondParticipant->is_manager)->toBeFalse();
});

it('throws exception when adding participant to direct message', function () {
    $conversation = Conversation::factory()->direct()->create();
    $user = User::factory()->create();

    app(AddParticipant::class)(
        conversation: $conversation,
        user: $user,
    );
})->throws(InvalidArgumentException::class, 'Cannot add participants to direct message conversations.');
