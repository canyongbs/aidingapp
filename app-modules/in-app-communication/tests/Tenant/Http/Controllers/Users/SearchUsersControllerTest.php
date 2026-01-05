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

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->realtimeChat = true;
    $settings->save();
});

it('returns users matching the search query by name', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create(['name' => 'Searcher', 'email' => 'searcher@example.com']);

    User::factory()->create(['name' => 'John Doe', 'email' => 'johndoe@example.com']);
    User::factory()->create(['name' => 'Jane Smith', 'email' => 'janesmith@example.com']);

    actingAs($user)
        ->getJson(route('in-app-communication.users.search', ['query' => 'John']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'John Doe');
});

it('searches users case insensitively by name', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create(['name' => 'Searcher', 'email' => 'searcher@example.com']);

    User::factory()->create(['name' => 'John Doe', 'email' => 'johndoe@example.com']);

    actingAs($user)
        ->getJson(route('in-app-communication.users.search', ['query' => 'john']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'John Doe');
});

it('searches users case insensitively by email', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create(['name' => 'Searcher', 'email' => 'searcher@example.com']);

    User::factory()->create(['name' => 'Target User', 'email' => 'John.Doe@Example.com']);

    actingAs($user)
        ->getJson(route('in-app-communication.users.search', ['query' => 'john.doe@example']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.email', 'John.Doe@Example.com');
});

it('returns users matching the search query by email', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create(['name' => 'Searcher', 'email' => 'searcher@example.com']);

    $matchingUser = User::factory()->create(['name' => 'Matching User', 'email' => 'john@example.com']);
    $nonMatchingUser = User::factory()->create(['name' => 'Non Matching User', 'email' => 'jane@example.com']);

    actingAs($user)
        ->getJson(route('in-app-communication.users.search', ['query' => 'john@']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.email', 'john@example.com');
});

it('excludes the current user from search results', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create(['name' => 'Current User', 'email' => 'current@example.com']);
    $otherUser = User::factory()->create(['name' => 'Other User', 'email' => 'other@example.com']);

    actingAs($user)
        ->getJson(route('in-app-communication.users.search', ['query' => 'User']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Other User');
});

it('returns all users when no search query is provided', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    User::factory()->count(5)->create();

    actingAs($user)
        ->getJson(route('in-app-communication.users.search'))
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

it('limits results to 25 users', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    User::factory()->count(30)->create();

    actingAs($user)
        ->getJson(route('in-app-communication.users.search'))
        ->assertOk()
        ->assertJsonCount(25, 'data');
});

it('returns user data with correct structure', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $searchUser = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    actingAs($user)
        ->getJson(route('in-app-communication.users.search', ['query' => 'Test']))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'email', 'avatar_url'],
            ],
        ])
        ->assertJsonPath('data.0.name', 'Test User')
        ->assertJsonPath('data.0.email', 'test@example.com');
});

it('validates request', function (array $data, array $expectedErrors) {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->getJson(route('in-app-communication.users.search', $data))
        ->assertStatus(422)
        ->assertJsonValidationErrors($expectedErrors);
})->with([
    'query max length' => [
        ['query' => str_repeat('a', 256)],
        ['query' => 'The query may not be greater than 255 characters.'],
    ],
]);

it('requires authentication', function () {
    getJson(route('in-app-communication.users.search'))
        ->assertUnauthorized();
});

it('requires the realtime chat feature to be enabled', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->realtimeChat = false;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->getJson(route('in-app-communication.users.search'))
        ->assertForbidden();
});
