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

use AidingApp\Project\Enums\PipelineStageClassification;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectWorkPipelineWidget;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use App\Features\PipelineEntryStartDateFeature;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    asSuperAdmin(User::factory()->create());
});

it('defaults the widget to the oldest non-archived pipeline', function () {
    $project = Project::factory()->create();
    $oldest = Pipeline::factory()->for($project)->create();
    $newer = Pipeline::factory()->for($project)->create();
    $oldest->archive();

    livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
        ->assertSet('selectedPipelineId', $newer->getKey());
});

it('archives the checked pipeline and cascades to its tasks', function () {
    $project = Project::factory()->create();
    $active = Pipeline::factory()->for($project)->create();
    $other = Pipeline::factory()->for($project)->create();

    livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
        ->call('archivePipelineFromSwitcher', $other->getKey());

    expect($other->refresh()->isArchived())->toBeTrue()
        ->and($active->refresh()->isArchived())->toBeFalse();
});

it('archives the checked pipeline through the switcher footer archive action', function () {
    $project = Project::factory()->create();
    $active = Pipeline::factory()->for($project)->create();
    $other = Pipeline::factory()->for($project)->create();

    livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
        ->mountAction('selectPipeline')
        ->setActionData(['pipeline_id' => $active->getKey()])
        ->mountAction('archivePipeline')
        ->callMountedAction()
        ->assertSet('selectedPipelineId', $other->getKey());

    expect($active->refresh()->isArchived())->toBeTrue()
        ->and($other->refresh()->isArchived())->toBeFalse();
});

it('keeps the switcher modal open after archiving when other pipelines remain', function () {
    $project = Project::factory()->create();
    $active = Pipeline::factory()->for($project)->create();
    Pipeline::factory()->for($project)->create();

    livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
        ->mountAction('selectPipeline')
        ->setActionData(['pipeline_id' => $active->getKey()])
        ->mountAction('archivePipeline')
        ->callMountedAction()
        ->assertActionMounted('selectPipeline');
});

it('closes the switcher modal after archiving the last remaining pipeline', function () {
    $project = Project::factory()->create();
    $only = Pipeline::factory()->for($project)->create();

    livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
        ->mountAction('selectPipeline')
        ->setActionData(['pipeline_id' => $only->getKey()])
        ->mountAction('archivePipeline')
        ->callMountedAction()
        ->assertActionNotMounted();
});

it('re-selects the oldest remaining pipeline after archiving the active one', function () {
    $project = Project::factory()->create();
    $active = Pipeline::factory()->for($project)->create();
    $next = Pipeline::factory()->for($project)->create();

    livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
        ->assertSet('selectedPipelineId', $active->getKey())
        ->call('archivePipelineFromSwitcher', $active->getKey())
        ->assertSet('selectedPipelineId', $next->getKey());
});

it('clears the selection when the last pipeline is archived', function () {
    $project = Project::factory()->create();
    $only = Pipeline::factory()->for($project)->create();

    livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
        ->call('archivePipelineFromSwitcher', $only->getKey())
        ->assertSet('selectedPipelineId', null);
});

it('excludes archived tasks from the pipeline board', function () {
    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()->for($project)->create();
    $stage = PipelineStage::factory()->for($pipeline)->create(['classification' => PipelineStageClassification::Planning]);

    $active = PipelineEntry::factory()->for($stage, 'pipelineStage')->create([
        'assigned_to_id' => null,
        'assigned_to_type' => null,
    ]);
    $archived = PipelineEntry::factory()->for($stage, 'pipelineStage')->create([
        'assigned_to_id' => null,
        'assigned_to_type' => null,
    ]);
    $archived->archive();

    livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
        ->assertCanSeeTableRecords([$active])
        ->assertCanNotSeeTableRecords([$archived]);
});

it('does not provide a row-level edit action because task editing is accessed through the name column', function () {
    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()->for($project)->create();
    $stage = PipelineStage::factory()->for($pipeline)->create(['classification' => PipelineStageClassification::Planning]);
    $entry = PipelineEntry::factory()->for($stage, 'pipelineStage')->create();

    livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
        ->assertTableActionDoesNotExist('edit', record: $entry);
});

describe('status filter', function () {
    it('defaults to showing all non-complete classifications and hiding complete tasks', function () {
        $project = Project::factory()->create();
        $pipeline = Pipeline::factory()->for($project)->create();

        $planningStage = PipelineStage::factory()->for($pipeline)->create(['classification' => PipelineStageClassification::Planning]);
        $inProgressStage = PipelineStage::factory()->for($pipeline)->create(['classification' => PipelineStageClassification::InProgress]);
        $completeStage = PipelineStage::factory()->for($pipeline)->create(['classification' => PipelineStageClassification::Complete]);

        $planningTask = PipelineEntry::factory()->for($planningStage, 'pipelineStage')->create();
        $inProgressTask = PipelineEntry::factory()->for($inProgressStage, 'pipelineStage')->create();
        $completeTask = PipelineEntry::factory()->for($completeStage, 'pipelineStage')->create();

        livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
            ->assertCanSeeTableRecords([$planningTask, $inProgressTask])
            ->assertCanNotSeeTableRecords([$completeTask]);
    });

    it('can filter tasks by stage classification', function () {
        $project = Project::factory()->create();
        $pipeline = Pipeline::factory()->for($project)->create();

        $planningStage = PipelineStage::factory()->for($pipeline)->create(['classification' => PipelineStageClassification::Planning]);
        $completeStage = PipelineStage::factory()->for($pipeline)->create(['classification' => PipelineStageClassification::Complete]);

        $planningTask = PipelineEntry::factory()->for($planningStage, 'pipelineStage')->create();
        $completeTask = PipelineEntry::factory()->for($completeStage, 'pipelineStage')->create();

        livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
            ->filterTable('classification', [PipelineStageClassification::Complete->value])
            ->assertCanSeeTableRecords([$completeTask])
            ->assertCanNotSeeTableRecords([$planningTask]);
    });
});

it('archives a pipeline task from its slide-over modal', function () {
    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()->for($project)->create();
    $stage = PipelineStage::factory()->for($pipeline)->create(['classification' => PipelineStageClassification::Planning]);
    $entry = PipelineEntry::factory()->for($stage, 'pipelineStage')->create();

    expect($entry->isArchived())->toBeFalse();

    livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
        ->mountAction('editPipelineEntry', ['entry' => $entry->getKey()])
        ->mountAction(['editPipelineEntry', 'archivePipelineEntry'])
        ->callMountedAction()
        ->assertActionNotMounted()
        ->assertCanNotSeeTableRecords([$entry]);

    expect($entry->refresh()->isArchived())->toBeTrue();
});

describe('feature flags', function () {
    it('hides the Start Date column when the pipeline entry start date flag is inactive', function () {
        PipelineEntryStartDateFeature::deactivate();

        $project = Project::factory()->create();
        Pipeline::factory()->for($project)->create();

        livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
            ->assertTableColumnDoesNotExist('start_date');
    });
});

describe('archive authorization', function () {
    it('does not archive when the acting user lacks the pipeline delete ability', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('pipeline.view-any');
        $user->refresh();
        actingAs($user);

        $project = Project::factory()->create();
        $pipeline = Pipeline::factory()->for($project)->create();

        livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
            ->call('archivePipelineFromSwitcher', $pipeline->getKey());

        expect($pipeline->refresh()->isArchived())->toBeFalse();
    });

    it('hides the footer archive control from a user lacking the pipeline delete ability', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('pipeline.view-any');
        $user->refresh();
        actingAs($user);

        $project = Project::factory()->create();
        Pipeline::factory()->for($project)->create();

        livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
            ->mountAction('selectPipeline')
            ->assertActionHidden('archivePipeline');
    });
});

describe('task access authorization', function () {
    it('opens pipeline task details for a project auditor', function () {
        $auditor = User::factory()->create();
        $auditor->givePermissionTo('project.*.view');
        $auditor->refresh();

        $project = Project::factory()->create();
        $project->auditorUsers()->attach($auditor);
        $pipeline = Pipeline::factory()->for($project)->create();
        $stage = PipelineStage::factory()->for($pipeline)->create(['classification' => PipelineStageClassification::Planning]);
        $entry = PipelineEntry::factory()->for($stage, 'pipelineStage')->create(['name' => 'View-only task']);

        actingAs($auditor);

        livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
            ->callTableColumnAction('name', $entry)
            ->assertActionMounted('viewPipelineEntry')
            ->assertSchemaStateSet(['name' => 'View-only task'])
            ->unmountAction()
            ->assertActionHidden('editPipelineEntry');
    });

    it('opens an editable pipeline task form for a project manager', function () {
        $manager = User::factory()->create();
        $manager->givePermissionTo(['project.*.view', 'project.*.update']);
        $manager->refresh();

        $project = Project::factory()->create();
        $project->managerUsers()->attach($manager);
        $pipeline = Pipeline::factory()->for($project)->create();
        $stage = PipelineStage::factory()->for($pipeline)->create(['classification' => PipelineStageClassification::Planning]);
        $entry = PipelineEntry::factory()->for($stage, 'pipelineStage')->create(['description' => 'Original description.']);

        actingAs($manager);

        livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
            ->callTableColumnAction('name', $entry)
            ->assertActionMounted('editPipelineEntry')
            ->setActionData(['description' => 'Updated description.'])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        expect($entry->refresh()->description)->toBe('Updated description.');
    });
});
