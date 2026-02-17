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
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Event::fake();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->realtimeChat = true;
    $settings->save();
});

it('updates a channel conversation', function () {
    $user = User::factory()->create();

    $conversation = Conversation::factory()->channel()->create(['name' => 'Old Name']);
    ConversationParticipant::factory()->manager()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    actingAs($user)
        ->patchJson(route('in-app-communication.conversations.update', $conversation), [
            'name' => 'New Name',
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'New Name');
});

it('only allows managers to update conversations', function () {
    $user = User::factory()->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
        'is_manager' => false,
    ]);

    actingAs($user)
        ->patchJson(route('in-app-communication.conversations.update', $conversation), [
            'name' => 'New Name',
        ])
        ->assertForbidden();
});

it('validates request', function (array $data, array $expectedErrors) {
    $user = User::factory()->create();

    $conversation = Conversation::factory()->channel()->create();
    ConversationParticipant::factory()->manager()->create([
        'conversation_id' => $conversation->getKey(),
        'participant_id' => $user->getKey(),
    ]);

    actingAs($user)
        ->patchJson(route('in-app-communication.conversations.update', $conversation), $data)
        ->assertStatus(422)
        ->assertJsonValidationErrors($expectedErrors);
})->with([
    'name max length' => [
        ['name' => str_repeat('a', 256)],
        ['name' => 'The name may not be greater than 255 characters.'],
    ],
]);
