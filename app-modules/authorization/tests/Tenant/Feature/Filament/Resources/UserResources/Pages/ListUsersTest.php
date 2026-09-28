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

use AidingApp\Department\Models\Department;
use AidingApp\Group\Models\Group;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Authenticatable;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Contracts\Support\Htmlable;
use Lab404\Impersonate\Services\ImpersonateManager;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use STS\FilamentImpersonate\Actions\Impersonate;

use function Tests\asSuperAdmin;

it('renders impersonate button for non super admin users when user is super admin', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $component = livewire(ListUsers::class);

    $component
        ->assertSuccessful()
        ->assertCountTableRecords(2)
        ->assertTableActionVisible(Impersonate::class, $user);
});

it('does not render impersonate button for super admin users when user is not super admin', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()
        ->create()
        ->givePermissionTo('user.view-any', 'user.*.view');
    actingAs($user);

    $component = livewire(ListUsers::class);

    $component
        ->assertSuccessful()
        ->assertCountTableRecords(1)
        ->assertCanSeeTableRecords([$user])
        ->assertCanNotSeeTableRecords([$superAdmin]);
});

it('does not render impersonate button for super admin users at all', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();
    asSuperAdmin($user);

    $component = livewire(ListUsers::class);

    $component
        ->assertSuccessful()
        ->assertCountTableRecords(2)
        ->assertTableActionHidden(Impersonate::class, $superAdmin);
});

it('does not render impersonate button for super admin users even if user is also a Super Admin', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()
        ->create();

    $user->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    actingAs($user);

    $component = livewire(ListUsers::class);

    $component
        ->assertSuccessful()
        ->assertCountTableRecords(2)
        ->assertTableActionHidden(Impersonate::class, $superAdmin);
});

it('allows super admin user to impersonate', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();

    $component = livewire(ListUsers::class);

    $component
        ->assertSuccessful()
        ->assertCountTableRecords(2)
        ->callTableAction(Impersonate::class, $user);

    expect($user->isImpersonated())->toBeTrue()
        ->and(auth()->id())->toBe($user->id);
});

it('allows a user to leave impersonate', function () {
    $first = User::factory()->create();
    asSuperAdmin($first);

    $second = User::factory()->create();

    app(ImpersonateManager::class)->take($first, $second);

    expect($second->isImpersonated())->toBeTrue()
        ->and(auth()->id())->toBe($second->id);

    $second->leaveImpersonation();

    expect($second->isImpersonated())->toBeFalse()
        ->and(auth()->id())->toBe($first->id);
});

it('can filter users by departments', function () {
    asSuperAdmin();

    $department1 = Department::factory()->create();
    $department2 = Department::factory()->create();

    $userWithoutDepartment = User::factory()->count(5)->create();

    $userWithDepartment1 = User::factory()
        ->count(5)
        ->for($department1)
        ->create();

    $userWithDepartment2 = User::factory()
        ->count(5)
        ->for($department2)
        ->create();

    livewire(ListUsers::class)
        ->set('tableRecordsPerPage', 16)
        ->assertCanSeeTableRecords($userWithoutDepartment->merge($userWithDepartment1)->merge($userWithDepartment2))
        ->filterTable('department', [$department1->getKey()])
        ->assertCanSeeTableRecords($userWithDepartment1)
        ->assertCanNotSeeTableRecords($userWithoutDepartment->merge($userWithDepartment2))
        ->filterTable('department', [$department2->getKey()])
        ->assertCanSeeTableRecords($userWithDepartment2)
        ->assertCanNotSeeTableRecords($userWithoutDepartment->merge($userWithDepartment1))
        ->filterTable('department', [$department2->getKey(), $department1->getKey()])
        ->assertCanSeeTableRecords($userWithDepartment1->merge($userWithDepartment2))
        ->assertCanNotSeeTableRecords($userWithoutDepartment);
});

it('shows the import and export header actions to a user with the user.import permission', function () {
    $user = User::factory()
        ->create()
        ->givePermissionTo('user.view-any', 'user.*.view', 'user.import');
    actingAs($user);

    livewire(ListUsers::class)
        ->assertActionVisible('import')
        ->assertActionVisible('export');
});

it('hides the import and export header actions from a user without the user.import permission', function () {
    $user = User::factory()
        ->create()
        ->givePermissionTo('user.view-any', 'user.*.view');
    actingAs($user);

    livewire(ListUsers::class)
        ->assertActionHidden('import')
        ->assertActionHidden('export');
});

it('only shows the bulk delete action to a user with the user.delete permission', function () {
    User::factory(15)->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('user.view-any', 'user.*.view');

    actingAs($user);

    livewire(ListUsers::class)
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('user.*.delete');

    livewire(ListUsers::class)
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});

it('can search users by name', function () {
    asSuperAdmin();

    $matchingUser = User::factory()->create(['name' => 'John Doe']);
    $nonMatchingUser = User::factory()->create(['name' => 'Jane Smith']);

    livewire(ListUsers::class)
        ->searchTable('John Doe')
        ->assertCanSeeTableRecords([$matchingUser])
        ->assertCanNotSeeTableRecords([$nonMatchingUser]);
});

it('can search users by email', function () {
    asSuperAdmin();

    $matchingUser = User::factory()->create(['email' => 'searchable@example.com']);
    $nonMatchingUser = User::factory()->create(['email' => 'other@example.com']);

    livewire(ListUsers::class)
        ->searchTable('searchable@example.com')
        ->assertCanSeeTableRecords([$matchingUser])
        ->assertCanNotSeeTableRecords([$nonMatchingUser]);
});

describe('name column', function () {
    it('shows the Online presence label in the tooltip and aria-label for an active user', function () {
        asSuperAdmin();

        $user = User::factory()->create(['last_activity_at' => now()]);

        $component = livewire(ListUsers::class)
            ->assertTableColumnHasExtraAttributes('name', [
                'aria-label' => "{$user->name} (Presence: Online)",
            ], $user);

        $column = $component->instance()->getTable()->getColumn('name');
        $column->record($user);

        expect($column->getTooltip())->toBe('Presence: Online');
    });

    it('shows the copy email tooltip and message on the email description', function () {
        asSuperAdmin();

        $user = User::factory()->create();

        $column = livewire(ListUsers::class)->instance()->getTable()->getColumn('name');
        $column->record($user);

        expect($column->getDescriptionBelow()->getData())->toMatchArray([
            'text' => $user->email,
            'tooltip' => 'Copy Email Address',
            'copyMessage' => 'Email address copied to clipboard',
        ]);
    });
});

describe('service details column', function () {
    it('shows the job title as the state when it is set', function () {
        asSuperAdmin();

        $user = User::factory()->create(['job_title' => 'Software Engineer']);

        livewire(ListUsers::class)
            ->assertTableColumnStateSet('job_title', 'Software Engineer', $user)
            ->assertTableColumnFormattedStateSet('job_title', 'Software Engineer', $user)
            ->assertTableColumnHasDescription('job_title', null, $user);
    });

    it('shows the placeholder and does not center the column when the job title and service areas are both blank', function () {
        asSuperAdmin();

        $user = User::factory()->create(['job_title' => null]);

        livewire(ListUsers::class)
            ->assertTableColumnStateSet('job_title', null, $user)
            ->assertTableColumnHasDescription('job_title', null, $user);

        $column = livewire(ListUsers::class)->instance()->getTable()->getColumn('job_title');
        $column->record($user);

        expect($column->getPlaceholder())->toBe('—')
            ->and($column->getVerticalAlignment())->toBeNull();
    });

    it('renders the service details badge and centers the column when the job title is blank but service areas exist', function () {
        asSuperAdmin();

        $user = User::factory()->create(['job_title' => null]);

        $managedType = ServiceRequestType::factory()->create();
        $managedType->managerUsers()->attach($user);

        $column = livewire(ListUsers::class)->instance()->getTable()->getColumn('job_title');
        $column->record($user);

        expect($column->formatState($column->getState()))->toBe('')
            ->and($column->getVerticalAlignment())->toBe(VerticalAlignment::Center);

        $description = $column->getDescriptionBelow();

        expect($description)->not->toBeNull()
            ->and($description->getData()['label'])->toBe('1 Service Area');
    });

    it('renders the service details tooltip as HTML listing manager and auditor service areas', function () {
        asSuperAdmin();

        $user = User::factory()->create(['job_title' => null]);

        $managedType = ServiceRequestType::factory()->create();
        $managedType->managerUsers()->attach($user);

        $auditedType = ServiceRequestType::factory()->create();
        $auditedType->auditorUsers()->attach($user);

        $column = livewire(ListUsers::class)->instance()->getTable()->getColumn('job_title');
        $column->record($user);

        $tooltip = $column->getDescriptionBelow()->getData()['tooltip'];

        expect($tooltip)->toBeInstanceOf(Htmlable::class)
            ->and($tooltip->toHtml())->toBe("Manager (Agent): {$managedType->name}<br />Auditor: {$auditedType->name}");
    });
});

describe('associations column', function () {
    it('shows the building icon only when the user has a department', function () {
        asSuperAdmin();

        $department = Department::factory()->create();
        $userWithDepartment = User::factory()->for($department)->create();
        $userWithoutDepartment = User::factory()->create(['department_id' => null]);

        $column = livewire(ListUsers::class)->instance()->getTable()->getColumn('department.name');

        $column->record($userWithDepartment);
        expect($column->getIcon($column->getState()))->toBe('heroicon-o-building-office-2');

        $column->record($userWithoutDepartment);
        expect($column->getIcon($column->getState()))->toBeNull();
    });

    it('centers the column and shows the department tooltip when only the department is populated', function () {
        asSuperAdmin();

        $department = Department::factory()->create();
        $user = User::factory()->for($department)->create();

        $column = livewire(ListUsers::class)->instance()->getTable()->getColumn('department.name');
        $column->record($user);

        expect($column->getVerticalAlignment())->toBe(VerticalAlignment::Center)
            ->and($column->getTooltip())->toBe("Department(s): {$department->name}");
    });

    it('renders the groups badge and does not center the column when there is no department but groups exist', function () {
        asSuperAdmin();

        $group = Group::factory()->create(['name' => 'VIP']);
        $user = User::factory()->create(['department_id' => null]);
        $user->groups()->attach($group);

        $column = livewire(ListUsers::class)->instance()->getTable()->getColumn('department.name');
        $column->record($user);

        expect($column->formatState($column->getState()))->toBe('')
            ->and($column->getVerticalAlignment())->toBeNull()
            ->and($column->getIcon($column->getState()))->toBeNull();

        $description = $column->getDescriptionBelow();

        expect($description)->not->toBeNull()
            ->and($description->getData()['label'])->toBe('VIP');
    });

    it('does not center the column when both the department and groups are populated', function () {
        asSuperAdmin();

        $department = Department::factory()->create();
        $group = Group::factory()->create();
        $user = User::factory()->for($department)->create();
        $user->groups()->attach($group);

        $column = livewire(ListUsers::class)->instance()->getTable()->getColumn('department.name');
        $column->record($user);

        expect($column->getVerticalAlignment())->toBeNull();
    });
});

describe('hidden column search', function () {
    it('can search users by preferred name', function () {
        asSuperAdmin();

        $matchingUser = User::factory()->create(['preferred_name' => 'Ace']);
        $nonMatchingUser = User::factory()->create(['preferred_name' => 'Bee']);

        livewire(ListUsers::class)
            ->searchTable('Ace')
            ->assertCanSeeTableRecords([$matchingUser])
            ->assertCanNotSeeTableRecords([$nonMatchingUser]);
    });

    it('can search users by work extension', function () {
        asSuperAdmin();

        $matchingUser = User::factory()->create(['work_extension' => 12345]);
        $nonMatchingUser = User::factory()->create(['work_extension' => 98765]);

        livewire(ListUsers::class)
            ->searchTable('12345')
            ->assertCanSeeTableRecords([$matchingUser])
            ->assertCanNotSeeTableRecords([$nonMatchingUser]);
    });
});
