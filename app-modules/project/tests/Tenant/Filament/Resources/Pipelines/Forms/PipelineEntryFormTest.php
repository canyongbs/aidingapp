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
use AidingApp\Project\Filament\Resources\Pipelines\Forms\PipelineEntryForm;
use AidingApp\Project\Livewire\PipelineEntryKanban;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectMilestone;
use Filament\Forms\Components\Select;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('formats the related milestone label with its task count and completion percentage', function (int $tasksCount, int $completedTasksCount, string $expected) {
    $milestone = ProjectMilestone::factory()->make(['title' => 'Wash the Car']);
    $milestone->tasks_count = $tasksCount;
    $milestone->completed_tasks_count = $completedTasksCount;

    expect(PipelineEntryForm::milestoneLabel($milestone))->toBe($expected);
})->with([
    'no tasks' => [0, 0, 'Wash the Car (0 Tasks) 0% Complete'],
    'one task, none complete' => [1, 0, 'Wash the Car (1 Task) 0% Complete'],
    'multiple tasks, some complete' => [4, 1, 'Wash the Car (4 Tasks) 25% Complete'],
]);

it('lists related milestone options preloaded, searchable, alphabetically ordered, and formatted with task counts and completion percentage', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $inProgressStage = PipelineStage::factory()->create([
        'pipeline_id' => $pipeline->getKey(),
        'classification' => PipelineStageClassification::InProgress,
    ]);

    $completeStage = PipelineStage::factory()->create([
        'pipeline_id' => $pipeline->getKey(),
        'classification' => PipelineStageClassification::Complete,
    ]);

    $washTheCar = ProjectMilestone::factory()->create([
        'project_id' => $project->getKey(),
        'title' => 'Wash the Car',
    ]);
    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $completeStage->getKey(),
        'project_milestone_id' => $washTheCar->getKey(),
    ]);
    PipelineEntry::factory()->count(3)->create([
        'pipeline_stage_id' => $inProgressStage->getKey(),
        'project_milestone_id' => $washTheCar->getKey(),
    ]);

    $buildTheSolution = ProjectMilestone::factory()->create([
        'project_id' => $project->getKey(),
        'title' => 'Build the Solution',
    ]);
    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $completeStage->getKey(),
        'project_milestone_id' => $buildTheSolution->getKey(),
    ]);
    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $inProgressStage->getKey(),
        'project_milestone_id' => $buildTheSolution->getKey(),
    ]);

    $buildTheHouse = ProjectMilestone::factory()->create([
        'project_id' => $project->getKey(),
        'title' => 'Build the House',
    ]);

    $otherProjectMilestone = ProjectMilestone::factory()->create(['title' => 'Zzz Other Project Milestone']);

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $inProgressStage->getKey(),
    ]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->mountAction('editPipelineEntry', ['entry' => $entry->getKey()])
        ->assertFormFieldExists(
            'project_milestone_id',
            function (Select $select) use ($washTheCar, $buildTheSolution, $buildTheHouse, $otherProjectMilestone): bool {
                $options = $select->getOptions();

                expect($options)->toBe([
                    $buildTheHouse->getKey() => 'Build the House (0 Tasks) 0% Complete',
                    $buildTheSolution->getKey() => 'Build the Solution (2 Tasks) 50% Complete',
                    $washTheCar->getKey() => 'Wash the Car (4 Tasks) 25% Complete',
                ])->and($options)->not->toHaveKey($otherProjectMilestone->getKey());

                return $select->isSearchable()
                    && $select->isPreloaded()
                    && $select->getOptionsLimit() === 100;
            },
        );
});

it('still resolves the label and keeps the option selectable for an archived milestone still related to an entry', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $stage = PipelineStage::factory()->create([
        'pipeline_id' => $pipeline->getKey(),
        'classification' => PipelineStageClassification::Planning,
    ]);

    $archivedMilestone = ProjectMilestone::factory()->create([
        'project_id' => $project->getKey(),
        'title' => 'Archived Milestone',
    ]);

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $stage->getKey(),
        'project_milestone_id' => $archivedMilestone->getKey(),
    ]);

    $archivedMilestone->archive();

    expect($archivedMilestone->isArchived())->toBeTrue();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->mountAction('editPipelineEntry', ['entry' => $entry->getKey()])
        ->assertFormFieldExists(
            'project_milestone_id',
            function (Select $select) use ($archivedMilestone): bool {
                $options = $select->getOptions();

                return $select->getOptionLabel() === 'Archived Milestone (1 Task) 0% Complete'
                    && array_key_exists($archivedMilestone->getKey(), $options);
            },
        );
});

it('excludes an archived milestone with no related entries from the options', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $stage = PipelineStage::factory()->create(['pipeline_id' => $pipeline->getKey()]);

    $unusedArchivedMilestone = ProjectMilestone::factory()->create([
        'project_id' => $project->getKey(),
        'title' => 'Unused Archived Milestone',
    ]);
    $unusedArchivedMilestone->archive();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $stage->getKey(),
    ]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->mountAction('editPipelineEntry', ['entry' => $entry->getKey()])
        ->assertFormFieldExists(
            'project_milestone_id',
            function (Select $select) use ($unusedArchivedMilestone): bool {
                return ! array_key_exists($unusedArchivedMilestone->getKey(), $select->getOptions());
            },
        );
});
