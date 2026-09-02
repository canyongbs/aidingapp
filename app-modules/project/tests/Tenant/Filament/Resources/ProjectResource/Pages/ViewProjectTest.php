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
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\Project\Enums\PipelineStageClassification;
use AidingApp\Project\Enums\ProjectTab;
use AidingApp\Project\Filament\Resources\Pipelines\PipelineResource;
use AidingApp\Project\Filament\Resources\Projects\Pages\ViewProject;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectAccessWidget;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectDashboardHeaderWidget;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectFilesWidget;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectStatsWidget;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectWorkPipelineWidget;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectFile;
use AidingApp\Project\Models\ProjectMilestone;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Repeater;
use Illuminate\Auth\Access\Events\GateEvaluated;
use Illuminate\Support\Facades\Event;

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

it('cannot render an archived project', function () {
    loginAsUserWithProjectViewPermissions();

    $project = Project::factory()->create();
    $project->archive();

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertNotFound();
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

describe('tabs', function () {
    it('shows each tab the user can view', function (ProjectTab $tab) {
        loginAsUserWithProjectViewPermissions();

        $project = Project::factory()->create();

        livewire(ViewProject::class, ['record' => $project->getRouteKey()])
            ->assertSee($tab->getLabel())
            ->assertSeeHtml("wire:click=\"\$set('tab', '{$tab->value}')\"");
    })->with(ProjectTab::cases());

    it('defaults to the access tab when no tab is requested', function () {
        loginAsUserWithProjectViewPermissions();

        $project = Project::factory()->create();

        livewire(ViewProject::class, ['record' => $project->getRouteKey()])
            ->assertSet('tab', ProjectTab::Access->value);
    });

    it('falls back to the access tab when the requested tab is invalid', function () {
        loginAsUserWithProjectViewPermissions();

        $project = Project::factory()->create();

        livewire(ViewProject::class, [
            'record' => $project->getRouteKey(),
            'tab' => 'invalid',
        ])
            ->assertSet('tab', ProjectTab::Access->value);
    });

    it('denies access without the `project.view-any` permission even when the user can view the project', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('project.*.view');

        $project = Project::factory()->create();
        $project->managerUsers()->attach($user->getKey());

        actingAs($user);

        expect($user->can('view', $project))->toBeTrue()
            ->and($user->can('viewAny', Project::class))->toBeFalse();

        get(ViewProject::getUrl([
            'record' => $project->getRouteKey(),
        ]))
            ->assertForbidden();
    });

    it('does not make the files tab available when project management is inactive', function () {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->projectManagement = false;
        $settings->save();

        asSuperAdmin();

        $project = Project::factory()->create();

        expect(ProjectTab::Files->canView($project))->toBeFalse();
    });

    it('renders only the active tab widget', function (ProjectTab $tab) {
        asSuperAdmin();

        $project = Project::factory()->create();

        $page = get(ViewProject::getUrl([
            'record' => $project->getRouteKey(),
            'tab' => $tab->value,
        ]))
            ->assertSuccessful()
            ->assertSeeLivewire(match ($tab) {
                ProjectTab::Access => ProjectAccessWidget::class,
                ProjectTab::Pipelines => ProjectWorkPipelineWidget::class,
                ProjectTab::Files => ProjectFilesWidget::class,
            });

        collect(ProjectTab::cases())
            ->reject(fn (ProjectTab $inactiveTab): bool => $inactiveTab === $tab)
            ->each(fn (ProjectTab $inactiveTab) => $page->assertDontSeeLivewire(match ($inactiveTab) {
                ProjectTab::Access => ProjectAccessWidget::class,
                ProjectTab::Pipelines => ProjectWorkPipelineWidget::class,
                ProjectTab::Files => ProjectFilesWidget::class,
            }));
    })->with(ProjectTab::cases());
});

it('can render the project access widget and mount the manage access action', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    livewire(ProjectAccessWidget::class, [
        'record' => $project,
    ])
        ->assertActionExists('manageAccess')
        ->mountAction('manageAccess')
        ->assertHasNoErrors();
});

it('can list pipeline entries in the project work pipeline widget', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Planning]), 'stages')
        ->create();

    $entries = PipelineEntry::factory()
        ->count(3)
        ->create(['pipeline_stage_id' => $pipeline->stages->sole()->getKey()]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertCanSeeTableRecords($entries);
});

it('hides archived pipeline entries in the project work pipeline widget', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Planning]), 'stages')
        ->create();
    $stage = $pipeline->stages->sole();

    $active = PipelineEntry::factory()->create(['pipeline_stage_id' => $stage->getKey()]);
    $archived = PipelineEntry::factory()->create(['pipeline_stage_id' => $stage->getKey()]);
    $archived->archive();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertCanSeeTableRecords([$active])
        ->assertCanNotSeeTableRecords([$archived]);
});

it('can create a milestone through the project milestones widget create action', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $milestone = ProjectMilestone::factory()->for($project)->make();

    livewire(ProjectWorkPipelineWidget::class, [
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

it('shows the create milestone action to users who can update the project', function () {
    $manager = User::factory()->create();
    $manager->givePermissionTo('project.view-any');
    $manager->givePermissionTo('project.*.view');
    $manager->givePermissionTo('project.*.update');

    $project = Project::factory()->create();
    $project->managerUsers()->attach($manager);

    actingAs($manager);

    Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertTableActionVisible('createMilestone');
});

it('hides the create milestone action from users who cannot update the project', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    actingAs($user);

    $project = Project::factory()->create();

    Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertTableActionHidden('createMilestone');
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
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Planning]), 'stages')
        ->create();

    $pipelineB = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Planning]), 'stages')
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

it('displays a pipeline entry start date in the project work pipeline widget', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Planning]), 'stages')
        ->create();
    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->sole()->getKey(),
        'start_date' => '2026-08-09 09:30:00',
    ]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertTableColumnStateSet('start_date', $entry->start_date, $entry);
});

it('displays a pipeline entry created by and customer visibility in the project work pipeline widget', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Planning]), 'stages')
        ->create();
    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->sole()->getKey(),
        'is_visible_to_guests' => true,
    ]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertTableColumnStateSet('createdBy.name', $entry->createdBy->name, $entry)
        ->assertTableColumnStateSet('is_visible_to_guests', true, $entry);
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

it('rejects selecting an archived pipeline', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $archivedPipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $archivedPipeline->archive();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->callAction('selectPipeline', data: [
            'pipeline_id' => $archivedPipeline->getKey(),
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

    PipelineEntry::factory()->create(['pipeline_stage_id' => $stage->getKey()]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->callTableAction('createEntry', data: [
            'name' => 'Kickoff Task',
            'pipeline_stage_id' => $stage->getKey(),
        ])
        ->assertHasNoTableActionErrors();

    expect(
        PipelineEntry::query()
            ->where('name', 'Kickoff Task')
            ->where('pipeline_stage_id', $stage->getKey())
            ->exists()
    )->toBeTrue();
});

it('displays milestone progress from completed pipeline tasks', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Planning]), 'stages')
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Complete]), 'stages')
        ->create();

    $milestone = ProjectMilestone::factory()->for($project)->create();

    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->firstWhere('classification', PipelineStageClassification::Planning)->getKey(),
        'project_milestone_id' => $milestone->getKey(),
    ]);
    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->firstWhere('classification', PipelineStageClassification::Complete)->getKey(),
        'project_milestone_id' => $milestone->getKey(),
    ]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertSee($milestone->title)
        ->assertSee('Progress: 50%');
});

it('groups unassigned pipeline tasks after milestone groups', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Planning]), 'stages')
        ->create();
    $milestone = ProjectMilestone::factory()->for($project)->create(['title' => 'Named Milestone']);

    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
        'project_milestone_id' => $milestone->getKey(),
    ]);
    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
    ]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertSeeInOrder([$milestone->title, 'No Associated Milestone']);
});

it('shows a manage milestone action when the user can manage the project', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Planning]), 'stages')
        ->create();
    $milestone = ProjectMilestone::factory()->for($project)->create();
    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
        'project_milestone_id' => $milestone->getKey(),
    ]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertSeeHtml("wire:click=\"mountAction('manageMilestone', { milestone: '{$milestone->getKey()}' })\"")
        ->assertSee($milestone->title)
        ->assertDontSee('Manage');
});

it('does not show a manage milestone action when the user cannot manage the project', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    actingAs($user);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Planning]), 'stages')
        ->create();
    $milestone = ProjectMilestone::factory()->for($project)->create();
    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
        'project_milestone_id' => $milestone->getKey(),
    ]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertSee($milestone->title)
        ->assertDontSeeHtml("wire:click=\"mountAction('manageMilestone', { milestone: '{$milestone->getKey()}' })\"");
});

it('renders the milestone title as plain text and denies the manage milestone action for view-only users', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    actingAs($user);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Planning]), 'stages')
        ->create();
    $milestone = ProjectMilestone::factory()->for($project)->create();
    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
        'project_milestone_id' => $milestone->getKey(),
    ]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertSeeHtml('<span>' . e($milestone->title) . '</span>')
        ->assertActionHidden(TestAction::make('manageMilestone')->arguments(['milestone' => $milestone->getKey()]));
});

it('renders a clean aria-label for the collapsible milestone group toggle instead of leaking the manage action markup', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Planning]), 'stages')
        ->create();
    $milestone = ProjectMilestone::factory()->for($project)->create(['title' => 'Alpha']);
    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
        'project_milestone_id' => $milestone->getKey(),
    ]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertSeeHtml("aria-label=\"{$milestone->title}\"");
});

it('does not re-authorize milestone updates per milestone group row', function () {
    $manager = User::factory()->create();
    $manager->givePermissionTo('project.view-any');
    $manager->givePermissionTo('project.*.view');
    $manager->givePermissionTo('project.*.update');

    $project = Project::factory()->create();
    $project->managerUsers()->attach($manager);

    actingAs($manager);

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    ProjectMilestone::factory()
        ->count(5)
        ->for($project)
        ->create()
        ->each(function (ProjectMilestone $milestone) use ($pipeline): void {
            PipelineEntry::factory()->create([
                'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
                'project_milestone_id' => $milestone->getKey(),
            ]);
        });

    $milestoneUpdateChecks = 0;

    Event::listen(GateEvaluated::class, function (GateEvaluated $event) use (&$milestoneUpdateChecks): void {
        if ($event->ability === 'update' && ($event->arguments[0] ?? null) instanceof ProjectMilestone) {
            $milestoneUpdateChecks++;
        }
    });

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ]);

    expect($milestoneUpdateChecks)->toBe(0);
});

it('deletes a milestone and leaves its pipeline tasks unassigned', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();
    $milestone = ProjectMilestone::factory()->for($project)->create();
    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
        'project_milestone_id' => $milestone->getKey(),
    ]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->callAction([
            TestAction::make('manageMilestone')->arguments(['milestone' => $milestone->getKey()]),
            TestAction::make('deleteMilestone'),
        ])
        ->assertHasNoActionErrors();

    expect($milestone->fresh()->trashed())->toBeTrue()
        ->and($entry->fresh()->project_milestone_id)->toBeNull();
});

it('shows the delete milestone action to users who can manage the project', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $milestone = ProjectMilestone::factory()->for($project)->create();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertActionVisible([
            TestAction::make('manageMilestone')->arguments(['milestone' => $milestone->getKey()]),
            'deleteMilestone',
        ]);
});

it('hides the delete milestone action from users who cannot manage the project', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    actingAs($user);

    $project = Project::factory()->create();
    $milestone = ProjectMilestone::factory()->for($project)->create();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertActionHidden([
            TestAction::make('manageMilestone')->arguments(['milestone' => $milestone->getKey()]),
            'deleteMilestone',
        ]);
});

it('does not show a soft-deleted milestone as a phantom empty group after the table resets', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();
    $milestone = ProjectMilestone::factory()->for($project)->create(['title' => 'Milestone To Delete']);
    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
        'project_milestone_id' => $milestone->getKey(),
    ]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->callAction([
            TestAction::make('manageMilestone')->arguments(['milestone' => $milestone->getKey()]),
            TestAction::make('deleteMilestone'),
        ])
        ->assertHasNoActionErrors()
        ->assertDontSee('No tasks yet');
});

it('shows milestones that have no pipeline tasks as an empty group', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();
    $milestone = ProjectMilestone::factory()->for($project)->create(['title' => 'Empty Milestone']);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertSee($milestone->title)
        ->assertTableColumnStateSet('name', 'No tasks yet', record: $milestone->getKey());
});

it('disables the name column click for placeholder rows but keeps it clickable for real entries', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Planning]), 'stages')
        ->create();
    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->sole()->getKey(),
    ]);
    $milestone = ProjectMilestone::factory()->for($project)->create(['title' => 'Empty Milestone']);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertTableColumnHasExtraAttributes('name', ['class' => 'underline'], $entry)
        ->assertTableColumnDoesNotHaveExtraAttributes('name', ['class' => 'underline'], $milestone);
});

it('does not show an archived or soft-deleted milestone as an empty group', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $archivedMilestone = ProjectMilestone::factory()->for($project)->create(['title' => 'Archived Milestone']);
    $archivedMilestone->archive();

    $deletedMilestone = ProjectMilestone::factory()->for($project)->create(['title' => 'Deleted Milestone']);
    $deletedMilestone->delete();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertDontSee('Archived Milestone')
        ->assertDontSee('Deleted Milestone');
});

it('keeps placeholder milestone rows visible when the classification filter excludes real entries', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Complete]), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->sole()->getKey(),
    ]);

    $emptyMilestone = ProjectMilestone::factory()->for($project)->create(['title' => 'Empty Milestone']);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->filterTable('classification', PipelineStageClassification::Planning)
        ->assertCanNotSeeTableRecords([$entry])
        ->assertSee($emptyMilestone->title)
        ->assertTableColumnStateSet('name', 'No tasks yet', record: $emptyMilestone->getKey());
});

it('updates a milestone from the manage slide-over', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();
    $milestone = ProjectMilestone::factory()->for($project)->create(['title' => 'Old Title']);
    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
        'project_milestone_id' => $milestone->getKey(),
    ]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->callAction(
            TestAction::make('manageMilestone')->arguments(['milestone' => $milestone->getKey()]),
            data: ['title' => 'New Title'],
        )
        ->assertHasNoActionErrors();

    expect($milestone->fresh()->title)->toBe('New Title');
});

it('clears related milestones, assets, and service requests on the widget edit action when type is set to none', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->state(['classification' => PipelineStageClassification::Planning]), 'stages')
        ->create();

    $stage = $pipeline->stages->first();

    $entry = PipelineEntry::factory()->create(['pipeline_stage_id' => $stage->getKey()]);

    $milestone = ProjectMilestone::factory()->create(['project_id' => $project->id]);
    $asset = Asset::factory()->create();
    $serviceRequest = ServiceRequest::factory()->create();

    $entry->update(['project_milestone_id' => $milestone->id]);
    $entry->assets()->sync([$asset->id]);
    $entry->serviceRequests()->sync([$serviceRequest->id]);

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->mountAction('editPipelineEntry', ['entry' => $entry->getKey()])
        ->setActionData([
            'name' => $entry->name,
            'pipeline_stage_id' => $stage->getKey(),
            'project_milestone_id' => null,
            'assets_type' => 'none',
            'service_requests_type' => 'none',
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    $entry->refresh();

    expect($entry->project_milestone_id)->toBeNull();
    expect($entry->assets->pluck('id')->all())->toBe([]);
    expect($entry->serviceRequests->pluck('id')->all())->toBe([]);
});

it('hides the header create entry action while the pipeline has no entries', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertTableActionHidden('createEntry')
        ->assertSee('Add Pipeline Task');
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

it('shows the kanban header action once a pipeline is selected and links to the entries page in kanban mode', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertTableActionVisible('kanban')
        ->assertTableActionHasUrl('kanban', PipelineResource::getUrl('entries', [
            'record' => $pipeline->getKey(),
            'project' => $project->getKey(),
        ]));
});

it('hides the kanban header action when the project has no pipelines', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    livewire(ProjectWorkPipelineWidget::class, [
        'record' => $project,
    ])
        ->assertTableActionHidden('kanban');
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

it('shows the manage files action to users who can update the project', function () {
    $manager = User::factory()->create();
    $manager->givePermissionTo('project.view-any');
    $manager->givePermissionTo('project.*.view');
    $manager->givePermissionTo('project.*.update');

    $project = Project::factory()->create();
    $project->managerUsers()->attach($manager);

    actingAs($manager);

    livewire(ProjectFilesWidget::class, [
        'record' => $project,
    ])
        ->assertTableActionVisible('manageFiles');
});

it('hides the manage files action from users who cannot update the project', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    actingAs($user);

    $project = Project::factory()->create();

    livewire(ProjectFilesWidget::class, [
        'record' => $project,
    ])
        ->assertTableActionHidden('manageFiles');
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
