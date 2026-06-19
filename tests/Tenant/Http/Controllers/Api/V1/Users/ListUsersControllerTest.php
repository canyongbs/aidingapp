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

use AidingApp\Authorization\Models\Permission;
use AidingApp\Authorization\Models\PermissionGroup;
use AidingApp\Authorization\Models\Role;
use AidingApp\Department\Models\Department;
use App\Models\Authenticatable;
use App\Models\SystemUser;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.users.index', [], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.view-any');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.users.index', [], false))
        ->assertOk();
});

it('returns a paginated list of users', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.view-any');
    Sanctum::actingAs($user, ['api']);

    User::factory()->count(3)->create();

    $response = getJson(route('api.v1.users.index', [], false));
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
        'links',
        'meta',
    ]);

    expect($response['data'])->toHaveCount(3);
});

it('returns correct user resource fields', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.view-any');
    Sanctum::actingAs($user, ['api']);

    $role = Role::factory()->create();
    $permissionGroup = PermissionGroup::create(['name' => 'test-group']);
    $permission = Permission::factory()->create(['guard_name' => 'web', 'group_id' => $permissionGroup->id]);
    $role->givePermissionTo($permission);

    $department = Department::factory()->create();
    $targetUser = User::factory()->create();
    $targetUser->roles()->attach($role);
    $targetUser->assignDepartment($department->id);

    $response = getJson(route('api.v1.users.index', [], false));
    $response->assertOk();

    $item = collect($response['data'])->firstWhere('id', $targetUser->id);

    expect($item['id'])->toBe($targetUser->id);
    expect($item['name'])->toBe($targetUser->name);
    expect($item['email'])->toBe($targetUser->email);
    expect($item['roles'])->toBe([$role->name]);
    expect($item['permissions'])->toBe([$permission->name]);
    expect($item['department'])->toBe([
        'id' => $department->id,
        'name' => $department->name,
    ]);
});

it('excludes users with admin roles from the list', function (string $adminRole) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.view-any');
    Sanctum::actingAs($user, ['api']);

    $regularUser = User::factory()->create();
    $adminUser = User::factory()->create();
    $adminUser->assignRole($adminRole);

    $response = getJson(route('api.v1.users.index', [], false));
    $response->assertOk();

    $returnedIds = collect($response['data'])->pluck('id');

    expect($returnedIds)->toContain($regularUser->id);
    expect($returnedIds)->not->toContain($adminUser->id);
})->with([
    'SaaS Global Admin' => [Authenticatable::SUPER_ADMIN_ROLE],
    'Partner Admin' => [Authenticatable::PARTNER_ADMIN_ROLE],
    'AI Admin' => [Authenticatable::AI_ADMIN_ROLE],
]);

it('can filter users by name', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.view-any');
    Sanctum::actingAs($user, ['api']);

    $matchingUser = User::factory()->create(['name' => 'John Doe']);
    User::factory()->create(['name' => 'Jane Smith']);
    User::factory()->create(['name' => 'Alice Brown']);

    $response = getJson(route('api.v1.users.index', ['filter' => ['name' => 'John']], false));
    $response->assertOk();

    expect($response['meta']['total'])->toBe(1);
    expect($response['data'][0]['id'])->toBe($matchingUser->id);
    expect($response['data'][0]['name'])->toBe('John Doe');
});

it('can filter users by department', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.view-any');
    Sanctum::actingAs($user, ['api']);

    $department = Department::factory()->create(['name' => 'Engineering']);
    $matchingUser = User::factory()->create();
    $matchingUser->assignDepartment($department->id);

    User::factory()->count(2)->create();

    // Exact name
    $response = getJson(route('api.v1.users.index', ['filter' => ['department' => 'Engineering']], false));
    $response->assertOk();

    expect($response['meta']['total'])->toBe(1);
    expect($response['data'][0]['id'])->toBe($matchingUser->id);
    expect($response['data'][0]['department']['name'])->toBe('Engineering');

    // Partial name
    $response = getJson(route('api.v1.users.index', ['filter' => ['department' => 'Engin']], false));
    $response->assertOk();

    expect($response['meta']['total'])->toBe(1);
    expect($response['data'][0]['id'])->toBe($matchingUser->id);
});

dataset('user_sorts', [
    '`name`' => ['name',  ['name' => 'Alpha User'],         ['name' => 'Zulu User'],         'name',  'Alpha User',         'Zulu User'],
    '`email`' => ['email', ['email' => 'alpha@example.com'], ['email' => 'zulu@example.com'], 'email', 'alpha@example.com', 'zulu@example.com'],
]);

it('can sort users by all attributes ascending', function (string $requestKey, array $firstAttributes, array $secondAttributes, string $responseKey, mixed $responseFirstValue, mixed $responseSecondValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.view-any');
    Sanctum::actingAs($user, ['api']);

    User::factory()->create($firstAttributes);
    User::factory()->create($secondAttributes);

    $response = getJson(route('api.v1.users.index', ['sort' => $requestKey], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])->toBe($responseFirstValue);
    expect($response['data'][1][$responseKey])->toBe($responseSecondValue);
})->with('user_sorts');

it('can sort users by all attributes descending', function (string $requestKey, array $firstAttributes, array $secondAttributes, string $responseKey, mixed $responseFirstValue, mixed $responseSecondValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.view-any');
    Sanctum::actingAs($user, ['api']);

    User::factory()->create($firstAttributes);
    User::factory()->create($secondAttributes);

    $response = getJson(route('api.v1.users.index', ['sort' => '-' . $requestKey], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])->toBe($responseSecondValue);
    expect($response['data'][1][$responseKey])->toBe($responseFirstValue);
})->with('user_sorts');
