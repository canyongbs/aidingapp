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

it('creates a channel conversation', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $participant = User::factory()->create();

    actingAs($user)
        ->postJson(route('in-app-communication.conversations.store'), [
            'type' => 'channel',
            'name' => 'New Channel',
            'participant_ids' => [$participant->getKey()],
            'is_private' => true,
        ])
        ->assertCreated()
        ->assertJsonPath('data.type', 'channel')
        ->assertJsonPath('data.name', 'New Channel')
        ->assertJsonPath('data.is_private', true);
});

it('creates a direct message conversation', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $participant = User::factory()->create();

    actingAs($user)
        ->postJson(route('in-app-communication.conversations.store'), [
            'type' => 'direct',
            'participant_ids' => [$participant->getKey()],
        ])
        ->assertCreated()
        ->assertJsonPath('data.type', 'direct');
});

it('requires exactly one participant for direct messages', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $participant1 = User::factory()->create();
    $participant2 = User::factory()->create();

    actingAs($user)
        ->postJson(route('in-app-communication.conversations.store'), [
            'type' => 'direct',
            'participant_ids' => [$participant1->getKey(), $participant2->getKey()],
        ])
        ->assertStatus(422);
});

it('requires at least one participant for direct messages', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->postJson(route('in-app-communication.conversations.store'), [
            'type' => 'direct',
            'participant_ids' => [],
        ])
        ->assertStatus(422);
});

it('validates request', function (array $data, array $expectedErrors) {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->postJson(route('in-app-communication.conversations.store'), $data)
        ->assertStatus(422)
        ->assertJsonValidationErrors($expectedErrors);
})->with([
    'type is required' => [
        ['name' => 'Test'],
        ['type' => 'The type field is required.'],
    ],
    'type must be valid' => [
        ['type' => 'invalid'],
        ['type' => 'The selected type is invalid.'],
    ],
    'name is required for channels' => [
        ['type' => 'channel'],
        ['name' => 'The name field is required when type is channel.'],
    ],
    'name max length' => [
        ['type' => 'channel', 'name' => str_repeat('a', 256)],
        ['name' => 'The name may not be greater than 255 characters.'],
    ],
    'participant_ids must be array' => [
        ['type' => 'channel', 'name' => 'Test', 'participant_ids' => 'not-array'],
        ['participant_ids' => 'The participant ids must be an array.'],
    ],
    'participant_ids must contain valid uuids' => [
        ['type' => 'channel', 'name' => 'Test', 'participant_ids' => ['invalid-uuid']],
        ['participant_ids.0' => 'The participant_ids.0 must be a valid UUID.'],
    ],
]);
