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
use AidingApp\Department\Models\Department;
use App\Models\Authenticatable;
use App\Models\SystemUser;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

beforeEach(function () {
    config()->set('audit.enabled', false);
});

it('returns user profile details', function () {
    $systemUser = SystemUser::create(['name' => 'Test API User']);

    $department = Department::factory()->create();
    $user = User::factory()->create();
    $user->assignDepartment($department->id);

    $role = Role::query()->firstOrCreate(['name' => 'test-role', 'guard_name' => 'web']);
    $user->roles()->attach($role);

    Sanctum::actingAs($systemUser, ['api']);

    getJson(route('api.v1.users.show', ['user' => $user], false))
        ->assertOk()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.name', $user->name)
        ->assertJsonPath('data.email', $user->email)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'roles',
                'department',
                'permissions',
            ],
        ]);
});

it('returns 404 when the requested user has an admin role', function () {
    $systemUser = SystemUser::create(['name' => 'Test API User Admin Check']);

    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    Sanctum::actingAs($systemUser, ['api']);

    getJson(route('api.v1.users.show', ['user' => $adminUser], false))
        ->assertNotFound();
});

it('returns 401 when called without authentication', function () {
    $user = User::factory()->create();

    getJson(route('api.v1.users.show', ['user' => $user], false))
        ->assertUnauthorized();
});

it('returns 403 when the token does not have the api ability', function () {
    $systemUser = SystemUser::create(['name' => 'Test API User No Ability']);
    Sanctum::actingAs($systemUser, ['other-ability']);

    $user = User::factory()->create();

    getJson(route('api.v1.users.show', ['user' => $user], false))
        ->assertForbidden();
});
