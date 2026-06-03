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
use App\Filament\Exports\UserExporter;
use App\Models\User;
use Filament\Actions\Exports\Models\Export;

/**
 * @return array<int, mixed>
 */
function exportUserRow(User $user): array
{
    $columnMap = [
        'name' => 'Name',
        'email' => 'Email address',
        'job_title' => 'Job title',
        'is_external' => 'External User',
        'work_number' => 'Work Number',
        'work_extension' => 'Work Extension',
        'mobile' => 'Mobile number',
        'department.name' => 'Department',
        'roles' => 'Assigned Role',
    ];

    $exporter = new UserExporter(
        export: new Export(),
        columnMap: $columnMap,
        options: [],
    );

    return $exporter($user);
}

it('exports all user fields, the department name, and roles joined with a pipe', function () {
    $department = Department::factory()->create(['name' => 'Engineering']);
    Role::factory()->create(['name' => 'Manager', 'guard_name' => 'web']);
    Role::factory()->create(['name' => 'Advisor', 'guard_name' => 'web']);

    $user = User::factory()
        ->for($department)
        ->create([
            'name' => 'Jonathan Smith',
            'email' => 'jonathan@example.com',
            'job_title' => 'Advisor',
            'work_number' => '+1 555 123 4567',
            'work_extension' => 123,
            'mobile' => '+1 555 987 6543',
            'is_external' => false,
        ]);

    $user->syncRoles(['Manager', 'Advisor']);

    $row = exportUserRow($user);

    expect($row[0])->toBe('Jonathan Smith')
        ->and($row[1])->toBe('jonathan@example.com')
        ->and($row[2])->toBe('Advisor')
        ->and($row[3])->toBe('false')
        ->and($row[4])->toBe('+1 555 123 4567')
        ->and((string) $row[5])->toBe('123')
        ->and($row[6])->toBe('+1 555 987 6543')
        ->and($row[7])->toBe('Engineering');

    expect(explode('|', $row[8]))
        ->toContain('Manager')
        ->toContain('Advisor');
});

it('exports is_external as true for external users', function () {
    $user = User::factory()->create(['is_external' => true]);

    expect(exportUserRow($user)[3])->toBe('true');
});

it('exports a blank roles value when the user has no web-guard roles', function () {
    $user = User::factory()->create();

    expect(blank(exportUserRow($user)[8]))->toBeTrue();
});
