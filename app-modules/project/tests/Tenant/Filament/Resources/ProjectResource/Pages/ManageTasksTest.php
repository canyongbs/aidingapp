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

use AidingApp\Contact\Models\Contact;
use AidingApp\Project\Filament\Resources\Projects\Pages\ManageTasks;
use AidingApp\Project\Models\Project;
use AidingApp\Task\Enums\TaskStatus;
use AidingApp\Task\Models\Task;
use App\Filament\Forms\Components\UserSelect;
use App\Models\Authenticatable;
use App\Models\User;
use Illuminate\Support\Facades\Config;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('cannot render without proper permission.', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();

    get(ManageTasks::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('can render with proper permission.', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    actingAs($user);

    $project = Project::factory()->create();

    get(ManageTasks::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->revokePermissionTo('project.view-any');
    $user->revokePermissionTo('project.*.view');
    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.view');

    $user->refresh();

    get(ManageTasks::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    get(ManageTasks::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can list pending/in_progress tasks', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    Task::factory()->count(10)->for($project)->create();

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertCanSeeTableRecords(
            $project->tasks
                ->whereIn('status', ['pending', 'in_progress'])
        )
        ->assertCanNotSeeTableRecords($project->tasks
            ->whereIn('status', ['completed', 'canceled']));
});

it('can search tasks by related contact', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $matchingContact = Contact::factory()->create([
        'first_name' => 'Zebediah',
        'last_name' => 'Stone',
        'full_name' => 'Zebediah Stone',
    ]);
    $otherContact = Contact::factory()->create([
        'first_name' => 'Amelia',
        'last_name' => 'Hart',
        'full_name' => 'Amelia Hart',
    ]);

    $matchingTask = Task::factory()
        ->for($project)
        ->state(['status' => TaskStatus::Pending->value])
        ->concerningContact($matchingContact)
        ->create();

    $otherTask = Task::factory()
        ->for($project)
        ->state(['status' => TaskStatus::InProgress->value])
        ->concerningContact($otherContact)
        ->create();

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->searchTable('zebediah')
        ->assertCanSeeTableRecords([$matchingTask])
        ->assertCanNotSeeTableRecords([$otherTask]);
});

// UserSelect admin-filtering tests

it('assigned_to UserSelect does not show admin users by default in create task form', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $regularUser = User::factory()->create();
    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    livewire(ManageTasks::class, ['record' => $project->getRouteKey()])
        ->assertSuccessful()
        ->mountTableAction('create')
        ->assertFormFieldExists('assigned_to', 'mountedTableActionSchema0', function (UserSelect $field) use ($regularUser, $adminUser): bool {
            return ! empty($field->getSearchResults($regularUser->name))
                && empty($field->getSearchResults($adminUser->name));
        });
});

it('confidential_task_users UserSelect does not show admin users by default in create task form', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $regularUser = User::factory()->create();
    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    livewire(ManageTasks::class, ['record' => $project->getRouteKey()])
        ->assertSuccessful()
        ->mountTableAction('create')
        ->assertFormFieldExists('confidential_task_users', 'mountedTableActionSchema0', function (UserSelect $field) use ($regularUser, $adminUser): bool {
            return ! empty($field->getSearchResults($regularUser->name))
                && empty($field->getSearchResults($adminUser->name));
        });
});

it('confidential_task_users UserSelect shows pre-selected admin when already on the task relationship', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    $task = Task::factory()->for($project)->create();
    $task->confidentialAccessUsers()->attach($adminUser);

    livewire(ManageTasks::class, ['record' => $project->getRouteKey()])
        ->assertSuccessful()
        ->mountTableAction('edit', $task)
        ->assertFormFieldExists('confidential_task_users', 'mountedTableActionSchema0', function (UserSelect $field) use ($adminUser): bool {
            return ! empty($field->getSearchResults($adminUser->name));
        });
});

it('assigned_to UserSelect shows all users when filter_admins_from_selection config is false', function () {
    Config::set('internal-users.filter_admins_from_selection', false);

    asSuperAdmin();

    $project = Project::factory()->create();
    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    livewire(ManageTasks::class, ['record' => $project->getRouteKey()])
        ->assertSuccessful()
        ->mountTableAction('create')
        ->assertFormFieldExists('assigned_to', 'mountedTableActionSchema0', function (UserSelect $field) use ($adminUser): bool {
            return ! empty($field->getSearchResults($adminUser->name));
        });
});
