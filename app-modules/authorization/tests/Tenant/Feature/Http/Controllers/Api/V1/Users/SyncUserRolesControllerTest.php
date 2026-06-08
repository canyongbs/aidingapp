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

use AidingApp\Authorization\Models\Role;
use App\Models\Authenticatable;
use App\Models\SystemUser;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\putJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $targetUser = User::factory()->create();

    $actingUser = SystemUser::factory()->create();
    Sanctum::actingAs($actingUser, ['api']);
    putJson(route('api.v1.users.roles.sync', ['user' => $targetUser], false), [
        'role_ids' => [],
    ])
        ->assertForbidden();

    $actingUser = SystemUser::factory()->create();
    $actingUser->givePermissionTo('user.*.update');
    Sanctum::actingAs($actingUser, ['api']);
    putJson(route('api.v1.users.roles.sync', ['user' => $targetUser], false), [
        'role_ids' => [],
    ])
        ->assertOk();
});

it('returns a user resource', function () {
    $actingUser = SystemUser::factory()->create();
    $actingUser->givePermissionTo('user.*.update');

    $targetUser = User::factory()->create();
    Sanctum::actingAs($actingUser, ['api']);

    $response = putJson(route('api.v1.users.roles.sync', ['user' => $targetUser], false), [
        'role_ids' => [],
    ]);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);
});

it('returns correct user fields', function (string $responseKey, Closure $getExpected) {
    $actingUser = SystemUser::factory()->create();
    $actingUser->givePermissionTo('user.*.update');

    $targetUser = User::factory()->create();
    Sanctum::actingAs($actingUser, ['api']);

    $response = putJson(route('api.v1.users.roles.sync', ['user' => $targetUser], false), [
        'role_ids' => [],
    ]);
    $response->assertOk();

    expect($response['data'][$responseKey])->toBe($getExpected($targetUser));
})->with([
    // responseKey, getExpected
    '`id`' => ['id', fn (User $user) => $user->id],
    '`name`' => ['name', fn (User $user) => $user->name],
    '`email`' => ['email', fn (User $user) => $user->email],
]);

it('returns correct roles relationship structure', function () {
    $actingUser = SystemUser::factory()->create();
    $actingUser->givePermissionTo('user.*.update');

    $role = Role::factory()->create();
    $targetUser = User::factory()->create();
    Sanctum::actingAs($actingUser, ['api']);

    $response = putJson(route('api.v1.users.roles.sync', ['user' => $targetUser], false), [
        'role_ids' => [$role->id],
    ]);
    $response->assertOk();

    expect($response['data']['roles'])->toBe([
        $role->name,
    ]);
});

it('returns an empty roles list when no role_ids are provided', function () {
    $actingUser = SystemUser::factory()->create();
    $actingUser->givePermissionTo('user.*.update');

    $targetUser = User::factory()->create();
    Sanctum::actingAs($actingUser, ['api']);

    $response = putJson(route('api.v1.users.roles.sync', ['user' => $targetUser], false), [
        'role_ids' => [],
    ]);
    $response->assertOk();

    expect($response['data']['roles'])->toBe([]);
});

it('returns 404 if the target user is an admin', function () {
    $adminRole = Role::firstOrCreate([
        'name' => Authenticatable::SUPER_ADMIN_ROLE,
        'guard_name' => 'web',
    ]);

    $targetUser = User::factory()->create();
    $targetUser->roles()->sync([$adminRole->id]);

    $actingUser = SystemUser::factory()->create();
    $actingUser->givePermissionTo('user.*.update');
    Sanctum::actingAs($actingUser, ['api']);

    putJson(route('api.v1.users.roles.sync', ['user' => $targetUser], false), [
        'role_ids' => [],
    ])
        ->assertNotFound();
});

it('returns 422 if any provided role_ids correspond to admin roles', function () {
    $adminRole = Role::firstOrCreate([
        'name' => Authenticatable::SUPER_ADMIN_ROLE,
        'guard_name' => 'web',
    ]);

    $targetUser = User::factory()->create();

    $actingUser = SystemUser::factory()->create();
    $actingUser->givePermissionTo('user.*.update');
    Sanctum::actingAs($actingUser, ['api']);

    putJson(route('api.v1.users.roles.sync', ['user' => $targetUser], false), [
        'role_ids' => [$adminRole->id],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['role_ids']);
});

it('returns 422 if any provided role_ids do not exist', function () {
    $targetUser = User::factory()->create();

    $actingUser = SystemUser::factory()->create();
    $actingUser->givePermissionTo('user.*.update');
    Sanctum::actingAs($actingUser, ['api']);

    putJson(route('api.v1.users.roles.sync', ['user' => $targetUser], false), [
        'role_ids' => [(string) Str::uuid()],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['role_ids.0']);
});

it('adds roles in the list that were not previously assigned', function () {
    $actingUser = SystemUser::factory()->create();
    $actingUser->givePermissionTo('user.*.update');

    $role = Role::factory()->create();
    $targetUser = User::factory()->create();
    Sanctum::actingAs($actingUser, ['api']);

    putJson(route('api.v1.users.roles.sync', ['user' => $targetUser], false), [
        'role_ids' => [$role->id],
    ])
        ->assertOk();

    expect($targetUser->fresh()->roles->pluck('id')->toArray())
        ->toBe([$role->id]);
});

it('removes roles not in the list', function () {
    $actingUser = SystemUser::factory()->create();
    $actingUser->givePermissionTo('user.*.update');

    $roleToKeep = Role::factory()->create();
    $roleToRemove = Role::factory()->create();
    $targetUser = User::factory()->create();
    $targetUser->roles()->sync([$roleToKeep->id, $roleToRemove->id]);
    Sanctum::actingAs($actingUser, ['api']);

    putJson(route('api.v1.users.roles.sync', ['user' => $targetUser], false), [
        'role_ids' => [$roleToKeep->id],
    ])
        ->assertOk();

    expect($targetUser->fresh()->roles->pluck('id')->toArray())
        ->toBe([$roleToKeep->id]);
});

it('removes all roles when an empty role_ids list is provided', function () {
    $actingUser = SystemUser::factory()->create();
    $actingUser->givePermissionTo('user.*.update');

    $role = Role::factory()->create();
    $targetUser = User::factory()->create();
    $targetUser->roles()->sync([$role->id]);
    Sanctum::actingAs($actingUser, ['api']);

    putJson(route('api.v1.users.roles.sync', ['user' => $targetUser], false), [
        'role_ids' => [],
    ])
        ->assertOk();

    expect($targetUser->fresh()->roles)->toBeEmpty();
});
