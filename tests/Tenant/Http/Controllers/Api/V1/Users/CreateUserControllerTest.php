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
use App\Notifications\SetPasswordNotification;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\postJson;

beforeEach(function () {
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $payload = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'is_external' => false,
    ];

    // Unauthenticated request is rejected
    postJson(route('api.v1.users.store', absolute: false), $payload)
        ->assertUnauthorized();

    // Authenticated but without permission is rejected
    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.users.store', absolute: false), $payload)
        ->assertForbidden();

    // With 'user.create' permission → success
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.users.store', absolute: false), $payload)
        ->assertCreated();
});

it('creates a user and returns a 201 response with required fields', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    $payload = [
        'name' => 'Jane Doe',
        'email' => 'jane.doe@example.com',
        'is_external' => false,
    ];

    $response = postJson(route('api.v1.users.store', absolute: false), $payload);

    $response->assertCreated();
    $response->assertJsonStructure(['data' => ['id', 'name', 'email', 'roles', 'department', 'permissions']]);

    expect($response['data']['name'])->toBe('Jane Doe');
    expect($response['data']['email'])->toBe('jane.doe@example.com');
    expect($response['data']['department'])->toBeNull();
    expect($response['data']['roles'])->toBeEmpty();
});

it('persists the new user to the database', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.users.store', absolute: false), [
        'name' => 'Persisted User',
        'email' => 'persisted@example.com',
        'is_external' => false,
    ])->assertCreated();

    expect(User::where('email', 'persisted@example.com')->exists())->toBeTrue();
});

it('creates a user with all optional fields', function () {
    $department = Department::factory()->create(['name' => 'Engineering']);
    $role = Role::factory()->create(['name' => 'custom-role', 'guard_name' => 'api']);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    $response = postJson(route('api.v1.users.store', absolute: false), [
        'name' => 'Full User',
        'email' => 'full@example.com',
        'is_external' => false,
        'job_title' => 'Engineer',
        'phone_number' => '555-1234',
        'work_number' => '555-5678',
        'work_extension' => 101,
        'mobile' => '555-9999',
        'department' => 'Engineering',
        'roles' => ['custom-role'],
    ]);

    $response->assertCreated();

    $created = User::where('email', 'full@example.com')->firstOrFail();

    expect($created->job_title)->toBe('Engineer');
    expect($created->phone_number)->toBe('555-1234');
    expect($created->work_number)->toBe('555-5678');
    expect($created->work_extension)->toBe(101);
    expect($created->mobile)->toBe('555-9999');
    expect($created->department_id)->toBe($department->id);
    expect($created->hasRole($role))->toBeTrue();
});

it('sends SetPasswordNotification when is_external is false', function () {
    Notification::fake();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.users.store', absolute: false), [
        'name' => 'Internal User',
        'email' => 'internal@example.com',
        'is_external' => false,
    ])->assertCreated();

    $created = User::where('email', 'internal@example.com')->firstOrFail();

    Notification::assertSentTo($created, SetPasswordNotification::class);
});

it('does not send SetPasswordNotification when is_external is true', function () {
    Notification::fake();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.users.store', absolute: false), [
        'name' => 'External User',
        'email' => 'external@example.com',
        'is_external' => true,
    ])->assertCreated();

    $created = User::where('email', 'external@example.com')->firstOrFail();

    Notification::assertNotSentTo($created, SetPasswordNotification::class);
});

it('resolves department case-insensitively', function () {
    $department = Department::factory()->create(['name' => 'Human Resources']);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    $response = postJson(route('api.v1.users.store', absolute: false), [
        'name' => 'HR User',
        'email' => 'hr@example.com',
        'is_external' => false,
        'department' => 'human resources',
    ]);

    $response->assertCreated();
    expect($response['data']['department']['id'])->toBe($department->id);
});

it('resolves role names case-insensitively', function () {
    $role = Role::factory()->create(['name' => 'Support Agent', 'guard_name' => 'api']);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    $response = postJson(route('api.v1.users.store', absolute: false), [
        'name' => 'Agent User',
        'email' => 'agent@example.com',
        'is_external' => false,
        'roles' => ['support agent'],
    ]);

    $response->assertCreated();

    $created = User::where('email', 'agent@example.com')->firstOrFail();
    expect($created->hasRole($role))->toBeTrue();
});

it('rejects a duplicate email address', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.users.store', absolute: false), [
        'name' => 'Duplicate',
        'email' => 'taken@example.com',
        'is_external' => false,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('rejects the email of a soft-deleted user', function () {
    $deleted = User::factory()->create(['email' => 'archived@example.com']);
    $deleted->delete();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.users.store', absolute: false), [
        'name' => 'Archived Duplicate',
        'email' => 'archived@example.com',
        'is_external' => false,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('returns 422 when department name does not exist', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.users.store', absolute: false), [
        'name' => 'No Dept User',
        'email' => 'nodept@example.com',
        'is_external' => false,
        'department' => 'Nonexistent Department',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['department']);
});

it('returns 422 when a role name does not exist', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.users.store', absolute: false), [
        'name' => 'Bad Role User',
        'email' => 'badrole@example.com',
        'is_external' => false,
        'roles' => ['nonexistent-role'],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['roles.0']);
});

it('rejects admin roles', function (string $adminRole) {
    Role::firstOrCreate(['name' => $adminRole, 'guard_name' => 'api']);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.users.store', absolute: false), [
        'name' => 'Admin Attempt',
        'email' => 'adminattempt@example.com',
        'is_external' => false,
        'roles' => [$adminRole],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['roles.0']);
})->with([
    'SaaS Global Admin' => [Authenticatable::SUPER_ADMIN_ROLE],
    'Partner Admin' => [Authenticatable::PARTNER_ADMIN_ROLE],
    'AI Admin' => [Authenticatable::AI_ADMIN_ROLE],
]);

it('rejects admin roles case-insensitively', function () {
    Role::firstOrCreate(['name' => Authenticatable::SUPER_ADMIN_ROLE, 'guard_name' => 'api']);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.users.store', absolute: false), [
        'name' => 'Admin Attempt',
        'email' => 'adminattempt2@example.com',
        'is_external' => false,
        'roles' => ['saas global admin'],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['roles.0']);
});

it('requires name, email, and is_external fields', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.users.store', absolute: false), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'is_external']);
});

it('rejects an invalid email format', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.users.store', absolute: false), [
        'name' => 'Bad Email',
        'email' => 'not-an-email',
        'is_external' => false,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('does not set a password on the created user', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.users.store', absolute: false), [
        'name' => 'No Password User',
        'email' => 'nopassword@example.com',
        'is_external' => false,
    ])->assertCreated();

    $created = User::where('email', 'nopassword@example.com')->firstOrFail();

    expect($created->password)->toBeNull();
});
