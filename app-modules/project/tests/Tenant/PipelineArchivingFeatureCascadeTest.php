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

use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectWorkPipelineWidget;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectMilestone;
use App\Models\User;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('archives a milestone once it has no remaining non-archived linked task', function () {
    $milestone = ProjectMilestone::factory()->create();
    $entry = PipelineEntry::factory()->create();
    $entry->milestones()->attach($milestone);

    $entry->archive();

    expect($milestone->refresh()->isArchived())->toBeTrue();
});

it('leaves a milestone with no linked tasks untouched', function () {
    $milestone = ProjectMilestone::factory()->create();

    $milestone->reevaluateArchivedState();

    expect($milestone->refresh()->isArchived())->toBeFalse();
});

it('keeps a milestone active while it still has a task in another active pipeline', function () {
    $milestone = ProjectMilestone::factory()->create();

    $entryA = PipelineEntry::factory()->create();
    $entryB = PipelineEntry::factory()->create();
    $entryA->milestones()->attach($milestone);
    $entryB->milestones()->attach($milestone);

    $entryA->archive();

    expect($milestone->refresh()->isArchived())->toBeFalse();

    $entryB->archive();

    expect($milestone->refresh()->isArchived())->toBeTrue();
});

it('archives all of a pipeline\'s tasks when the pipeline is archived', function () {
    $pipeline = Pipeline::factory()->create();
    $stage = PipelineStage::factory()->for($pipeline)->create();
    // Entries are created without an assignee: assignment is orthogonal to archiving,
    // and refreshing an assigned entry trips an unrelated pre-existing quirk in the
    // assignedTo() morphTo (relation name "assigned_to" has no matching method).
    $entries = PipelineEntry::factory()
        ->count(3)
        ->for($stage, 'pipelineStage')
        ->create([
            'assigned_to_id' => null,
            'assigned_to_type' => null,
        ]);

    $pipeline->archive();

    $entries->each(fn (PipelineEntry $entry) => expect($entry->refresh()->isArchived())->toBeTrue());
});

it('archives a milestone linked only to tasks in the archived pipeline', function () {
    $pipeline = Pipeline::factory()->create();
    $stage = PipelineStage::factory()->for($pipeline)->create();
    $entry = PipelineEntry::factory()->for($stage, 'pipelineStage')->create();
    $milestone = ProjectMilestone::factory()->create();
    $entry->milestones()->attach($milestone);

    $pipeline->archive();

    expect($milestone->refresh()->isArchived())->toBeTrue();
});

it('archives a pipeline\'s stages when the pipeline is archived', function () {
    $pipeline = Pipeline::factory()->create();
    $stages = PipelineStage::factory()->count(2)->for($pipeline)->create();

    $pipeline->archive();

    $stages->each(fn (PipelineStage $stage) => expect($stage->refresh()->isArchived())->toBeTrue());
});

it('archives a stage\'s non-archived tasks when the stage is archived', function () {
    $stage = PipelineStage::factory()->for(Pipeline::factory())->create();
    $entries = PipelineEntry::factory()
        ->count(3)
        ->for($stage, 'pipelineStage')
        ->create([
            'assigned_to_id' => null,
            'assigned_to_type' => null,
        ]);

    // One task is archived independently, before the stage cascade runs. The archive
    // cascade uses withoutArchived(), so the pre-archived task is left untouched.
    $preArchived = PipelineEntry::factory()->for($stage, 'pipelineStage')->create([
        'assigned_to_id' => null,
        'assigned_to_type' => null,
    ]);
    $preArchived->archive();

    $stage->archive();

    expect($stage->refresh()->isArchived())->toBeTrue();
    $entries->each(fn (PipelineEntry $entry) => expect($entry->refresh()->isArchived())->toBeTrue());
    expect($preArchived->refresh()->isArchived())->toBeTrue();
});

it('rolls back the whole cascade when a child archive fails through the switcher', function () {
    asSuperAdmin(User::factory()->create());

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()->for($project)->create();
    $stage = PipelineStage::factory()->for($pipeline)->create();
    PipelineEntry::factory()->for($stage, 'pipelineStage')->create([
        'assigned_to_id' => null,
        'assigned_to_type' => null,
    ]);

    // Force a failure partway through the cascade (after the pipeline and stage
    // rows have already been written, before the entry is archived).
    PipelineEntry::archiving(function (): void {
        throw new RuntimeException('boom');
    });

    try {
        livewire(ProjectWorkPipelineWidget::class, ['record' => $project])
            ->call('archivePipelineFromSwitcher', $pipeline->getKey());
    } catch (Throwable) {
        // Expected: the forced failure propagates out of the transaction.
    }

    expect($pipeline->refresh()->isArchived())->toBeFalse()
        ->and($stage->refresh()->isArchived())->toBeFalse();
});
