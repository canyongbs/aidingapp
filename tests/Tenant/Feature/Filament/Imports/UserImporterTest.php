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
use App\Filament\Imports\UserImporter;
use App\Models\User;
use App\Notifications\SetPasswordNotification;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

/**
 * @param  array<string, mixed>  $row
 */
function runUserImport(array $row): void
{
    $columnMap = collect(array_keys($row))
        ->mapWithKeys(fn (string $key): array => [$key => $key])
        ->all();

    $importer = new UserImporter(
        import: new Import(),
        columnMap: $columnMap,
        options: [],
    );

    $importer($row);
}

it('creates a new user with all fields, department and roles', function () {
    Notification::fake();

    $department = Department::factory()->create(['name' => 'Engineering']);
    Role::factory()->create(['name' => 'Manager', 'guard_name' => 'web']);
    Role::factory()->create(['name' => 'Advisor', 'guard_name' => 'web']);

    runUserImport([
        'name' => 'Jonathan Smith',
        'email' => 'jonathan@example.com',
        'job_title' => 'Advisor',
        'work_number' => '+1 555 123 4567',
        'work_extension' => '123',
        'mobile' => '+1 555 987 6543',
        'department' => 'Engineering',
        'is_external' => 'false',
        'roles' => 'Manager|Advisor',
    ]);

    $user = User::query()->where('email', 'jonathan@example.com')->firstOrFail();

    expect($user->name)->toBe('Jonathan Smith')
        ->and($user->job_title)->toBe('Advisor')
        ->and($user->work_number)->toBe('+1 555 123 4567')
        ->and($user->work_extension)->toBe(123)
        ->and($user->mobile)->toBe('+1 555 987 6543')
        ->and($user->is_external)->toBeFalse()
        ->and($user->department->is($department))->toBeTrue()
        ->and($user->roles->pluck('name')->sort()->values()->all())->toBe(['Advisor', 'Manager']);

    Notification::assertSentTo($user, SetPasswordNotification::class);
});

it('updates an existing user matched by email case-insensitively', function () {
    $department = Department::factory()->create(['name' => 'Support']);
    Role::factory()->create(['name' => 'Agent', 'guard_name' => 'web']);

    $existing = User::factory()->create([
        'email' => 'casey@example.com',
        'name' => 'Old Name',
        'job_title' => 'Old Title',
        'is_external' => true,
    ]);

    runUserImport([
        'name' => 'Casey New',
        'email' => 'CASEY@EXAMPLE.COM',
        'job_title' => 'New Title',
        'department' => 'Support',
        'roles' => 'Agent',
    ]);

    expect(User::query()->where('email', 'casey@example.com')->count())->toBe(1);

    $existing->refresh();

    expect($existing->name)->toBe('Casey New')
        ->and($existing->job_title)->toBe('New Title')
        ->and($existing->department->is($department))->toBeTrue()
        ->and($existing->roles->pluck('name')->all())->toBe(['Agent']);
});

it('replaces (syncs) roles, removing roles not listed', function () {
    Role::factory()->create(['name' => 'KeepMe', 'guard_name' => 'web']);
    Role::factory()->create(['name' => 'RemoveMe', 'guard_name' => 'web']);

    $user = User::factory()->create(['email' => 'sync@example.com']);
    $user->assignRole('RemoveMe');

    runUserImport([
        'name' => 'Sync User',
        'email' => 'sync@example.com',
        'job_title' => 'Title',
        'roles' => 'KeepMe',
    ]);

    expect($user->fresh()->roles->pluck('name')->all())->toBe(['KeepMe']);
});

it('leaves existing roles untouched when the roles column is blank', function () {
    Role::factory()->create(['name' => 'Existing', 'guard_name' => 'web']);

    $user = User::factory()->create(['email' => 'blank@example.com']);
    $user->assignRole('Existing');

    runUserImport([
        'name' => 'Blank Roles',
        'email' => 'blank@example.com',
        'job_title' => 'Title',
        'roles' => '',
    ]);

    expect($user->fresh()->roles->pluck('name')->all())->toBe(['Existing']);
});

it('resolves department and roles by name case-insensitively', function () {
    $department = Department::factory()->create(['name' => 'Finance']);
    Role::factory()->create(['name' => 'Reviewer', 'guard_name' => 'web']);

    runUserImport([
        'name' => 'Case User',
        'email' => 'case@example.com',
        'job_title' => 'Title',
        'department' => 'finance',
        'roles' => 'reviewer',
    ]);

    $user = User::query()->where('email', 'case@example.com')->firstOrFail();

    expect($user->department->is($department))->toBeTrue()
        ->and($user->roles->pluck('name')->all())->toBe(['Reviewer']);
});

it('fails the row when the department does not exist', function () {
    runUserImport([
        'name' => 'No Dept',
        'email' => 'nodept@example.com',
        'job_title' => 'Title',
        'department' => 'Nonexistent Department',
    ]);
})->throws(ValidationException::class);

it('fails the row when a role does not exist', function () {
    Role::factory()->create(['name' => 'RealRole', 'guard_name' => 'web']);

    runUserImport([
        'name' => 'No Role',
        'email' => 'norole@example.com',
        'job_title' => 'Title',
        'roles' => 'RealRole|FakeRole',
    ]);
})->throws(ValidationException::class);

it('fails the row when the email belongs to an archived user', function () {
    $archived = User::factory()->create(['email' => 'archived@example.com']);
    $archived->delete();

    runUserImport([
        'name' => 'Archived',
        'email' => 'archived@example.com',
        'job_title' => 'Title',
    ]);
})->throws(ValidationException::class);
