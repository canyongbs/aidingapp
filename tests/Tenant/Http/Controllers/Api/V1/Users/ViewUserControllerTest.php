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
    $targetUser = User::factory()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.users.show', ['user' => $targetUser], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.view-any');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.users.show', ['user' => $targetUser], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.*.view');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.users.show', ['user' => $targetUser], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['user.view-any', 'user.*.view']);
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.users.show', ['user' => $targetUser], false))
        ->assertOk();
});

it('returns a user resource', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['user.view-any', 'user.*.view']);
    Sanctum::actingAs($user, ['api']);

    $targetUser = User::factory()->create();

    $response = getJson(route('api.v1.users.show', ['user' => $targetUser], false));
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);
});

it('returns correct user fields', function (string $responseKey, Closure $getExpected) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['user.view-any', 'user.*.view']);
    Sanctum::actingAs($user, ['api']);

    $targetUser = User::factory()->create();

    $response = getJson(route('api.v1.users.show', ['user' => $targetUser], false));
    $response->assertOk();

    expect($response['data'][$responseKey])->toBe($getExpected($targetUser));
})->with([
    '`id`' => ['id', fn (User $user) => $user->id],
    '`name`' => ['name', fn (User $user) => $user->name],
    '`email`' => ['email', fn (User $user) => $user->email],
]);

it('returns correct roles relationship structure', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['user.view-any', 'user.*.view']);
    Sanctum::actingAs($user, ['api']);

    $targetUser = User::factory()->create();
    $role = Role::factory()->create();
    $targetUser->roles()->attach($role);

    $response = getJson(route('api.v1.users.show', ['user' => $targetUser], false));
    $response->assertOk();

    expect($response['data']['roles'])->toBe([$role->name]);
});

it('returns correct department relationship structure', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['user.view-any', 'user.*.view']);
    Sanctum::actingAs($user, ['api']);

    $department = Department::factory()->create();
    $targetUser = User::factory()->create();
    $targetUser->assignDepartment($department->id);

    $response = getJson(route('api.v1.users.show', ['user' => $targetUser], false));
    $response->assertOk();

    expect($response['data']['department'])->toBe([
        'id' => $department->id,
        'name' => $department->name,
    ]);
});

it('returns null department when no department is assigned', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['user.view-any', 'user.*.view']);
    Sanctum::actingAs($user, ['api']);

    $targetUser = User::factory()->create();

    $response = getJson(route('api.v1.users.show', ['user' => $targetUser], false));
    $response->assertOk();

    expect($response['data']['department'])->toBeNull();
});

it('returns correct permissions relationship structure', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['user.view-any', 'user.*.view']);
    Sanctum::actingAs($user, ['api']);

    $targetUser = User::factory()->create();
    $role = Role::factory()->create();
    $permissionGroup = PermissionGroup::create(['name' => 'test-group']);
    $permission = Permission::factory()->create(['guard_name' => 'web', 'group_id' => $permissionGroup->id]);
    $role->givePermissionTo($permission);
    $targetUser->roles()->attach($role);

    $response = getJson(route('api.v1.users.show', ['user' => $targetUser], false));
    $response->assertOk();

    expect($response['data']['permissions'])->toBe([$permission->name]);
});

it('returns 404 when the requested user has an admin role', function (string $adminRole) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['user.view-any', 'user.*.view']);
    Sanctum::actingAs($user, ['api']);

    $adminUser = User::factory()->create();
    $adminUser->assignRole($adminRole);

    getJson(route('api.v1.users.show', ['user' => $adminUser], false))
        ->assertNotFound();
})->with([
    'SaaS Global Admin' => [Authenticatable::SUPER_ADMIN_ROLE],
    'Partner Admin' => [Authenticatable::PARTNER_ADMIN_ROLE],
    'AI Admin' => [Authenticatable::AI_ADMIN_ROLE],
]);
