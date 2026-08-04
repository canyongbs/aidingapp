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
use AidingApp\Project\Enums\PipelineStageClassification;
use AidingApp\Project\Filament\Resources\Projects\Pages\ListProjects;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectAccessWidget;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectDashboardHeaderWidget;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Filament\Tables\Columns\Column;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('cannot render without proper permission.', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ListProjects::getUrl())
        ->assertForbidden();
});

it('can render with proper permission.', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    actingAs($user);

    get(ListProjects::getUrl())
        ->assertSuccessful();
});

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->projectManagement = false;
    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');

    actingAs($user);

    get(ListProjects::getUrl())->assertForbidden();

    $settings->data->addons->projectManagement = true;
    $settings->save();

    $user->revokePermissionTo('project.view-any');

    get(ListProjects::getUrl())->assertForbidden();

    $user->givePermissionTo('project.view-any');

    get(ListProjects::getUrl())->assertSuccessful();
});

it('can list records', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    actingAs($user);

    $records = Project::factory()->count(5)->create();

    livewire(ListProjects::class)
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($records)
        ->assertSuccessful();
});

it('does not list archived projects', function () {
    asSuperAdmin();

    $activeProject = Project::factory()->create();
    $archivedProject = Project::factory()->create();
    $archivedProject->archive();

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords([$activeProject])
        ->assertCanNotSeeTableRecords([$archivedProject])
        ->assertSuccessful();
});

it('can see project in list if logged in user is a superadmin, the creator, a manager, or an auditor of the project.', function () {
    $user = User::factory()->create();
    $secondUser = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    actingAs($user);

    $project = Project::factory()->create();

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords([$project])
        ->assertSuccessful();

    actingAs($secondUser);

    $secondUser->givePermissionTo('project.view-any');
    $secondUser->givePermissionTo('project.*.view');

    livewire(ListProjects::class)
        ->assertCanNotSeeTableRecords([$project])
        ->assertSuccessful();

    $project->managerUsers()->attach($secondUser->getKey());

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords([$project])
        ->assertSuccessful();

    $project->managerUsers()->detach($secondUser->getKey());

    livewire(ListProjects::class)
        ->assertCanNotSeeTableRecords([$project])
        ->assertSuccessful();

    $project->auditorUsers()->attach($secondUser->getKey());

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords([$project])
        ->assertSuccessful();

    $project->auditorUsers()->detach($secondUser->getKey());

    livewire(ListProjects::class)
        ->assertCanNotSeeTableRecords([$project])
        ->assertSuccessful();

    $department = Department::factory()->create();

    $secondUser->department()->associate($department)->save();

    $project->managerDepartments()->attach($department->getKey());

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords([$project])
        ->assertSuccessful();

    $project->managerDepartments()->detach($department->getKey());

    livewire(ListProjects::class)
        ->assertCanNotSeeTableRecords([$project])
        ->assertSuccessful();

    $project->auditorDepartments()->attach($department->getKey());

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords([$project])
        ->assertSuccessful();

    asSuperAdmin();

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords([$project])
        ->assertSuccessful();
});

it('only shows archive actions to a user with the project.delete permission', function () {
    $user = User::factory()
        ->create()
        ->givePermissionTo('project.view-any', 'project.*.view');

    actingAs($user);

    $project = Project::factory()->for($user, 'createdBy')->create();

    livewire(ListProjects::class)
        ->assertActionHidden(TestAction::make('archive')->table($project))
        ->assertActionHidden(TestAction::make('archive')->table()->bulk());

    $user->givePermissionTo('project.*.delete');

    livewire(ListProjects::class)
        ->assertActionVisible(TestAction::make('archive')->table($project))
        ->assertActionVisible(TestAction::make('archive')->table()->bulk());
});

it('can archive a project from the list row action', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    livewire(ListProjects::class)
        ->assertSuccessful()
        ->callTableAction('archive', record: $project->getKey());

    assertDatabaseHas('projects', [
        'id' => $project->getKey(),
    ]);

    expect($project->fresh()?->isArchived())->toBeTrue();
});

it('can archive multiple projects from the list bulk action', function () {
    asSuperAdmin();

    $projects = Project::factory()->count(3)->create();

    livewire(ListProjects::class)
        ->assertSuccessful()
        ->callTableBulkAction('archive', $projects);

    $projects->each(fn (Project $project) => expect($project->fresh()?->isArchived())->toBeTrue());
});

it('displays project name, manager(s), department, start date, target date, and progress columns', function () {
    asSuperAdmin();

    $department = Department::factory()->create(['name' => 'Enrollment']);

    $project = Project::factory()
        ->for($department)
        ->hasAttached(User::factory()->state(['name' => 'Casey Manager']), relationship: 'managerUsers')
        ->create([
            'start_date' => '2026-01-15',
            'target_completion_date' => null,
        ]);

    $pipeline = Pipeline::factory()->for($project)->create();

    PipelineEntry::factory()
        ->for(
            PipelineStage::factory()->for($pipeline)->state(['classification' => PipelineStageClassification::Complete]),
            'pipelineStage',
        )
        ->create();

    PipelineEntry::factory()
        ->for(
            PipelineStage::factory()->for($pipeline)->state(['classification' => PipelineStageClassification::Planning]),
            'pipelineStage',
        )
        ->create();

    livewire(ListProjects::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$project])
        ->assertTableColumnExists('name', fn (Column $column): bool => $column->getLabel() === 'Project Name')
        ->assertTableColumnExists('managers', fn (Column $column): bool => $column->getLabel() === 'Manager(s)')
        ->assertTableColumnExists('department.name', fn (Column $column): bool => $column->getLabel() === 'Department')
        ->assertTableColumnExists('start_date', fn (Column $column): bool => $column->getLabel() === 'Start Date')
        ->assertTableColumnExists('target_completion_date', fn (Column $column): bool => $column->getLabel() === 'Target Date')
        ->assertTableColumnExists('progress', fn (Column $column): bool => $column->getLabel() === 'Progress')
        ->assertTableColumnStateSet('name', $project->name, $project)
        ->assertTableColumnHasDescription('name', $project->description, $project)
        ->assertTableColumnStateSet('department.name', 'Enrollment', $project)
        ->assertTableColumnStateSet('start_date', $project->start_date, $project)
        ->assertTableColumnStateSet('target_completion_date', null, $project)
        ->assertTableColumnStateSet('progress', 50, $project)
        ->assertTableColumnExists(
            'managers',
            fn (Column $column): bool => $column->getState()->pluck('name')->all() === ['Casey Manager'],
            $project,
        )
        ->assertTableColumnExists(
            'target_completion_date',
            fn (Column $column): bool => $column->getPlaceholder() === 'Indefinite',
            $project,
        )
        ->assertTableColumnDoesNotExist('files_count')
        ->assertTableColumnDoesNotExist('pipelines_count')
        ->assertTableColumnDoesNotExist('milestones_count');
});

it('shows N/A when a project has no managers, no department, or no start date', function () {
    asSuperAdmin();

    $project = Project::factory()->create([
        'department_id' => null,
        'start_date' => null,
    ]);

    livewire(ListProjects::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$project])
        ->assertTableColumnStateSet('department.name', null, $project)
        ->assertTableColumnStateSet('start_date', null, $project)
        ->assertTableColumnExists(
            'department.name',
            fn (Column $column): bool => $column->getPlaceholder() === 'N/A',
            $project,
        )
        ->assertTableColumnExists(
            'start_date',
            fn (Column $column): bool => $column->getPlaceholder() === 'N/A',
            $project,
        )
        ->assertTableColumnExists(
            'managers',
            fn (Column $column): bool => $column->getState()->isEmpty(),
            $project,
        );
});

it('can search projects by project name, manager name, and department name', function () {
    asSuperAdmin();

    $matchingByName = Project::factory()->create(['name' => 'Onboarding Revamp']);
    $matchingByManager = Project::factory()->create(['name' => 'Unrelated Project']);
    $manager = User::factory()->create(['name' => 'Jordan Searchable']);
    $matchingByManager->managerUsers()->attach($manager->getKey());

    $department = Department::factory()->create(['name' => 'Searchable Department']);
    $matchingByDepartment = Project::factory()->for($department)->create(['name' => 'Another Project']);

    $otherDepartment = Department::factory()->create(['name' => 'Other Department']);
    User::factory()->for($otherDepartment)->create(['name' => 'Taylor Delegate']);

    $matchingByDepartmentManager = Project::factory()
        ->hasAttached($otherDepartment, relationship: 'managerDepartments')
        ->create(['name' => 'Yet Another Project']);

    $notMatching = Project::factory()->create(['name' => 'Completely Different']);

    livewire(ListProjects::class)
        ->searchTable('onboarding revamp')
        ->assertCanSeeTableRecords([$matchingByName])
        ->assertCanNotSeeTableRecords([$matchingByManager, $notMatching]);

    livewire(ListProjects::class)
        ->searchTable('jordan searchable')
        ->assertCanSeeTableRecords([$matchingByManager])
        ->assertCanNotSeeTableRecords([$matchingByName, $notMatching]);

    livewire(ListProjects::class)
        ->searchTable('searchable department')
        ->assertCanSeeTableRecords([$matchingByDepartment])
        ->assertCanNotSeeTableRecords([$matchingByName, $notMatching]);

    livewire(ListProjects::class)
        ->searchTable('taylor delegate')
        ->assertCanSeeTableRecords([$matchingByDepartmentManager])
        ->assertCanNotSeeTableRecords([$matchingByName, $notMatching]);
});

it('caps the manager avatars it renders but keeps every manager searchable', function () {
    asSuperAdmin();

    $project = Project::factory()
        ->hasAttached(User::factory()->count(24), relationship: 'managerUsers')
        ->hasAttached(User::factory()->state(['name' => 'Wilhelmina Ashgrove']), relationship: 'managerUsers')
        ->create(['name' => 'Crowded Project']);

    livewire(ListProjects::class)
        ->assertSuccessful()
        ->assertTableColumnExists(
            'managers',
            fn (Column $column): bool => $column->getState()->count() === 25,
            $project,
        )
        ->assertSeeHtml('+20');

    livewire(ListProjects::class)
        ->searchTable('Wilhelmina Ashgrove')
        ->assertCanSeeTableRecords([$project]);
});

it('shows the same managers on the list as the project dashboard', function () {
    asSuperAdmin();

    $department = Department::factory()
        ->has(User::factory()->count(2), 'users')
        ->create();

    $project = Project::factory()
        ->hasAttached(User::factory()->count(2), relationship: 'managerUsers')
        ->hasAttached($department, relationship: 'managerDepartments')
        ->create();

    $dashboardManagers = livewire(ProjectAccessWidget::class, ['record' => $project])
        ->instance()
        ->managers()
        ->pluck('id')
        ->sort()
        ->values()
        ->all();

    expect($dashboardManagers)->toHaveCount(4);

    livewire(ListProjects::class)
        ->assertTableColumnExists(
            'managers',
            fn (Column $column): bool => $column->getState()->pluck('id')->sort()->values()->all() === $dashboardManagers,
            $project,
        );
});

it('shows the same progress on the list as the project dashboard', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()->for($project)->create();

    PipelineEntry::factory()
        ->count(3)
        ->for(
            PipelineStage::factory()->for($pipeline)->state(['classification' => PipelineStageClassification::Planning]),
            'pipelineStage',
        )
        ->create();

    PipelineEntry::factory()
        ->for(
            PipelineStage::factory()->for($pipeline)->state(['classification' => PipelineStageClassification::Complete]),
            'pipelineStage',
        )
        ->create();

    $dashboardProgress = livewire(ProjectDashboardHeaderWidget::class, ['record' => $project])
        ->instance()
        ->getProgress();

    expect($dashboardProgress)->toBe(25);

    livewire(ListProjects::class)
        ->assertTableColumnStateSet('progress', $dashboardProgress, $project);
});

it('ranks a project named Test above a project managed by someone named Test', function () {
    asSuperAdmin();

    $managedByTest = Project::factory()
        ->hasAttached(User::factory()->state(['name' => 'Test']), relationship: 'managerUsers')
        ->create(['name' => 'Something Else']);

    $namedTest = Project::factory()->create(['name' => 'Test']);

    livewire(ListProjects::class)
        ->searchTable('Test')
        ->assertCanSeeTableRecords([$namedTest, $managedByTest], inOrder: true);
});

it('ranks project name matches above manager matches, and manager matches above department matches, when searching', function () {
    asSuperAdmin();

    $matchingByDepartment = Project::factory()
        ->for(Department::factory()->state(['name' => 'Test Department']))
        ->create(['name' => 'Gamma']);

    $matchingByManager = Project::factory()
        ->hasAttached(User::factory()->state(['name' => 'Test Manager']), relationship: 'managerUsers')
        ->create(['name' => 'Beta']);

    $matchingByName = Project::factory()->create(['name' => 'Test Alpha']);

    livewire(ListProjects::class)
        ->searchTable('Test')
        ->assertCanSeeTableRecords(
            [$matchingByName, $matchingByManager, $matchingByDepartment],
            inOrder: true,
        );
});

it('ranks project name matches first when the search words are given in another order', function () {
    asSuperAdmin();

    $matchingByManager = Project::factory()
        ->hasAttached(User::factory()->state(['name' => 'Casey Manager']), relationship: 'managerUsers')
        ->create(['name' => 'Alpha']);

    $matchingByName = Project::factory()->create(['name' => 'Zulu Casey Onboarding Manager']);

    livewire(ListProjects::class)
        ->searchTable('Manager Casey')
        ->assertCanSeeTableRecords([$matchingByName, $matchingByManager], inOrder: true);
});

it('ranks project name matches first when the search is a quoted phrase', function () {
    asSuperAdmin();

    $matchingByManager = Project::factory()
        ->hasAttached(User::factory()->state(['name' => 'Casey Manager']), relationship: 'managerUsers')
        ->create(['name' => 'Alpha']);

    $matchingByName = Project::factory()->create(['name' => 'Zulu Casey Manager Report']);

    livewire(ListProjects::class)
        ->searchTable('"Casey Manager"')
        ->assertCanSeeTableRecords([$matchingByName, $matchingByManager], inOrder: true);
});

it('does not fail when the search contains no searchable words', function () {
    asSuperAdmin();

    Project::factory()->create(['name' => 'Alpha']);

    livewire(ListProjects::class)
        ->searchTable('""')
        ->assertSuccessful();
});

it('sorts by the selected column instead of search relevance when a sort is applied', function () {
    asSuperAdmin();

    $matchingByName = Project::factory()->create(['name' => 'Test Alpha']);

    $matchingByManager = Project::factory()
        ->hasAttached(User::factory()->state(['name' => 'Test Manager']), relationship: 'managerUsers')
        ->create(['name' => 'Zulu Project']);

    livewire(ListProjects::class)
        ->searchTable('Test')
        ->sortTable('name', 'desc')
        ->assertCanSeeTableRecords([$matchingByManager, $matchingByName], inOrder: true);
});

it('can filter projects by department', function () {
    asSuperAdmin();

    $department = Department::factory()->create();
    $otherDepartment = Department::factory()->create();

    $projectInDepartment = Project::factory()->for($department)->create();
    $projectInOtherDepartment = Project::factory()->for($otherDepartment)->create();

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords([$projectInDepartment, $projectInOtherDepartment])
        ->filterTable('department', [$department->getKey()])
        ->assertCanSeeTableRecords([$projectInDepartment])
        ->assertCanNotSeeTableRecords([$projectInOtherDepartment]);
});
