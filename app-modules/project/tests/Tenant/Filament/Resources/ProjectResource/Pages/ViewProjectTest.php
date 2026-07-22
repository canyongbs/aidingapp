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
use AidingApp\Department\Models\Department;
use AidingApp\Project\Enums\PipelineStageClassification;
use AidingApp\Project\Filament\Resources\Projects\Pages\ManageManagers;
use AidingApp\Project\Filament\Resources\Projects\Pages\ViewProject;
use AidingApp\Project\Filament\Resources\Projects\RelationManagers\ManagerUsersRelationManager;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectAccessWidget;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectDashboardHeaderWidget;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectFilesWidget;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectMilestonesWidget;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectStatsWidget;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectWorkPipelineWidget;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectFile;
use AidingApp\Project\Models\ProjectMilestone;
use App\Models\User;
use Filament\Forms\Components\Repeater;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

function loginAsUserWithProjectViewPermissions(): User
{
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    actingAs($user);

    return $user;
}

it('cannot render without proper permission', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('can render with proper permission', function () {
    loginAsUserWithProjectViewPermissions();

    $project = Project::factory()->create();

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can render if logged in user is a superadmin, the creator, a manager, or an auditor of the project', function () {
    $user = User::factory()->create();
    $secondUser = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    actingAs($user);

    $project = Project::factory()->create();

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();

    actingAs($secondUser);

    $secondUser->givePermissionTo('project.view-any');
    $secondUser->givePermissionTo('project.*.view');

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertNotFound();

    $project->managerUsers()->attach($secondUser->getKey());

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();

    $project->managerUsers()->detach($secondUser->getKey());

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertNotFound();

    $project->auditorUsers()->attach($secondUser->getKey());

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();

    $project->auditorUsers()->detach($secondUser->getKey());

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertNotFound();

    $department = Department::factory()->create();

    $secondUser->department()->associate($department)->save();

    $project->managerDepartments()->attach($department->getKey());

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();

    $project->managerDepartments()->detach($department->getKey());

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertNotFound();

    $project->auditorDepartments()->attach($department->getKey());

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();

    asSuperAdmin();

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can view a record', function () {
    loginAsUserWithProjectViewPermissions();

    $project = Project::factory()->create();

    livewire(ViewProject::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertHasNoErrors();
});

it('can render the project access widget and mount the manage access action', function () {
    loginAsUserWithProjectViewPermissions();

    $project = Project::factory()->create();

    livewire(ProjectAccessWidget::class, [
        'record' => $project,
    ])
        ->assertActionExists('manageAccess')
        ->mountAction('manageAccess')
        ->assertHasNoErrors();
});

it('can attach a manager user through the centralized access relation manager', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $manager = User::factory()->create();

    livewire(ManagerUsersRelationManager::class, [
        'ownerRecord' => $project,
        'pageClass' => ManageManagers::class,
    ])
        ->callTableAction('attach', data: [
            'recordId' => $manager->getKey(),
        ])
        ->assertHasNoTableActionErrors();

    expect($project->managerUsers()->whereKey($manager->getKey())->exists())->toBeTrue();
});

it('can list milestones in the project milestones widget', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $milestones = ProjectMilestone::factory()->count(3)->for($project)->create();

    livewire(ProjectMilestonesWidget::class, [
        'record' => $project,
    ])
        ->assertCanSeeTableRecords($milestones);
});

it('can create a milestone through the project milestones widget create action', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $milestone = ProjectMilestone::factory()->for($project)->make();

    livewire(ProjectMilestonesWidget::class, [
        'record' => $project,
    ])
        ->callTableAction('createMilestone', data: [
            'title' => $milestone->title,
            'description' => $milestone->description,
            'status_id' => $milestone->status_id,
            'target_date' => $milestone->target_date,
        ])
        ->assertHasNoTableActionErrors();

    expect($project->milestones()->where('title', $milestone->title)->exists())->toBeTrue();
});

it('auto-selects the first pipeline on mount in the project work pipeline widget', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertSet('selectedPipelineId', $pipeline->getKey());
});

it('only shows pipeline entries that belong to the selected pipeline', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipelineA = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $pipelineB = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entriesA = PipelineEntry::factory()->count(2)->create([
        'pipeline_stage_id' => $pipelineA->stages->first()->getKey(),
    ]);

    $entriesB = PipelineEntry::factory()->count(2)->create([
        'pipeline_stage_id' => $pipelineB->stages->first()->getKey(),
    ]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->callAction('selectPipeline', data: ['pipeline_id' => $pipelineA->getKey()])
        ->assertCanSeeTableRecords($entriesA)
        ->assertCanNotSeeTableRecords($entriesB)
        ->callAction('selectPipeline', data: ['pipeline_id' => $pipelineB->getKey()])
        ->assertCanSeeTableRecords($entriesB)
        ->assertCanNotSeeTableRecords($entriesA);
});

it('can switch the selected pipeline through the select pipeline action', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $pipelineB = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertActionExists('selectPipeline')
        ->callAction('selectPipeline', data: [
            'pipeline_id' => $pipelineB->getKey(),
        ])
        ->assertHasNoActionErrors()
        ->assertSet('selectedPipelineId', $pipelineB->getKey());
});

it('rejects selecting a pipeline that belongs to another project', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $otherProject = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $foreignPipeline = Pipeline::factory()
        ->for($otherProject)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->callAction('selectPipeline', data: [
            'pipeline_id' => $foreignPipeline->getKey(),
        ])
        ->assertNotified('Invalid pipeline selection')
        ->assertSet('selectedPipelineId', $pipeline->getKey());
});

it('can create a pipeline through the create pipeline action', function () {
    $undoRepeaterFake = Repeater::fake();

    asSuperAdmin();

    $project = Project::factory()->create();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertActionExists('createPipeline')
        ->callAction('createPipeline', data: [
            'name' => 'Delivery Pipeline',
            'description' => 'Tracks delivery work.',
            'stages' => [
                ['name' => 'Planning'],
                ['name' => 'In Progress'],
                ['name' => 'Complete'],
            ],
        ])
        ->assertHasNoActionErrors()
        ->assertSet('selectedPipelineId', fn (?string $state): bool => filled($state));

    $pipeline = Pipeline::query()->where('name', 'Delivery Pipeline')->first();

    expect($pipeline)->not->toBeNull();
    expect($pipeline->project_id)->toBe($project->getKey());
    expect($pipeline->stages)->toHaveCount(3);

    $undoRepeaterFake();
});

it('can create a pipeline entry through the widget header create action', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $stage = $pipeline->stages->first();

    $contact = Contact::factory()->create();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->callTableAction('createEntry', data: [
            'name' => 'Kickoff Task',
            'pipeline_stage_id' => $stage->getKey(),
            'organizable_type' => $contact->getMorphClass(),
            'organizable_id' => $contact->getKey(),
        ])
        ->assertHasNoTableActionErrors();

    expect(
        PipelineEntry::query()
            ->where('name', 'Kickoff Task')
            ->where('pipeline_stage_id', $stage->getKey())
            ->exists()
    )->toBeTrue();
});

it('shows the empty state when the project has no pipelines', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertSet('selectedPipelineId', null)
        ->assertActionExists('createPipeline')
        ->assertSee('No pipeline selected');
});

it('can list files in the project files widget', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $files = ProjectFile::factory()->count(3)->for($project)->create();

    livewire(ProjectFilesWidget::class, [
        'record' => $project,
    ])
        ->assertCanSeeTableRecords($files);
});

it('calculates progress as 0 when the project has no pipeline entries', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(2), 'stages')
        ->create();

    livewire(ProjectDashboardHeaderWidget::class, [
        'record' => $project,
    ])
        ->assertSee('Progress: 0%');
});

it('calculates progress as the percentage of pipeline entries with a complete stage classification', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()->for($project)->create();

    $planningStage = PipelineStage::factory()->for($pipeline)->create([
        'classification' => PipelineStageClassification::Planning,
    ]);

    $completeStage = PipelineStage::factory()->for($pipeline)->create([
        'classification' => PipelineStageClassification::Complete,
    ]);

    PipelineEntry::factory()->count(3)->create(['pipeline_stage_id' => $planningStage->getKey()]);
    PipelineEntry::factory()->count(1)->create(['pipeline_stage_id' => $completeStage->getKey()]);

    livewire(ProjectDashboardHeaderWidget::class, [
        'record' => $project,
    ])
        ->assertSee('Progress: 25%');
});

it('only counts pipeline entries belonging to the given project when calculating progress', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $otherProject = Project::factory()->create();

    $pipeline = Pipeline::factory()->for($project)->create();
    $completeStage = PipelineStage::factory()->for($pipeline)->create([
        'classification' => PipelineStageClassification::Complete,
    ]);
    PipelineEntry::factory()->count(1)->create(['pipeline_stage_id' => $completeStage->getKey()]);

    $otherPipeline = Pipeline::factory()->for($otherProject)->create();
    $otherPlanningStage = PipelineStage::factory()->for($otherPipeline)->create([
        'classification' => PipelineStageClassification::Planning,
    ]);
    PipelineEntry::factory()->count(5)->create(['pipeline_stage_id' => $otherPlanningStage->getKey()]);

    livewire(ProjectDashboardHeaderWidget::class, [
        'record' => $project,
    ])
        ->assertSee('Progress: 100%');
});

it('gates the project access widget behind project view permissions', function () {
    $user = User::factory()->create();

    actingAs($user);

    expect(ProjectAccessWidget::canView())->toBeFalse();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->refresh();

    expect(ProjectAccessWidget::canView())->toBeTrue();
});

it('gates the project milestones widget behind project view permissions', function () {
    $user = User::factory()->create();

    actingAs($user);

    expect(ProjectMilestonesWidget::canView())->toBeFalse();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->refresh();

    expect(ProjectMilestonesWidget::canView())->toBeTrue();
});

it('gates the project files widget behind project view permissions', function () {
    $user = User::factory()->create();

    actingAs($user);

    expect(ProjectFilesWidget::canView())->toBeFalse();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->refresh();

    expect(ProjectFilesWidget::canView())->toBeTrue();
});

it('gates the project stats widget behind project view permissions', function () {
    $user = User::factory()->create();

    actingAs($user);

    expect(ProjectStatsWidget::canView())->toBeFalse();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->refresh();

    expect(ProjectStatsWidget::canView())->toBeTrue();
});

it('gates the project work pipeline widget behind the pipeline view-any permission', function () {
    $user = User::factory()->create();

    actingAs($user);

    expect(ProjectWorkPipelineWidget::canView())->toBeFalse();

    $user->givePermissionTo('pipeline.view-any');
    $user->refresh();

    expect(ProjectWorkPipelineWidget::canView())->toBeTrue();
});
