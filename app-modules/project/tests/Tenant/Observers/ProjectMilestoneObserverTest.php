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

use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectMilestone;

use function Tests\asSuperAdmin;

it('associates a new milestone with the project active pipeline', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $activePipeline = Pipeline::factory()->for($project)->create(['created_at' => now()->subDays(2)]);
    Pipeline::factory()->for($project)->create(['created_at' => now()->subDay()]);

    $milestone = $project->milestones()->create([
        'title' => 'MVP Ready',
        'description' => 'First release',
    ]);

    expect($milestone->pipeline_id)->toBe($activePipeline->getKey());
});

it('breaks ties by id to match the backfill ordering', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $sameMoment = now()->subDay();

    Pipeline::factory()->for($project)->create(['created_at' => $sameMoment]);
    Pipeline::factory()->for($project)->create(['created_at' => $sameMoment]);

    // Mirrors the deterministic ordering used to back-fill ProjectMilestone.pipeline_id, so the
    // observer and the migration always resolve the same active pipeline for same-second rows.
    $expectedPipelineId = Pipeline::query()
        ->where('project_id', $project->getKey())
        ->withoutArchived()
        ->orderBy('created_at')
        ->orderBy('id')
        ->value('id');

    $milestone = $project->milestones()->create([
        'title' => 'Tie Breaker',
        'description' => 'Same-second pipelines',
    ]);

    expect($milestone->pipeline_id)->toBe($expectedPipelineId);
});

it('ignores archived pipelines when choosing the active pipeline', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $archivedPipeline = Pipeline::factory()->for($project)->create(['created_at' => now()->subDays(3)]);
    $archivedPipeline->archive();

    $activePipeline = Pipeline::factory()->for($project)->create(['created_at' => now()->subDay()]);

    $milestone = $project->milestones()->create([
        'title' => 'Launch',
        'description' => 'Go live',
    ]);

    expect($milestone->pipeline_id)->toBe($activePipeline->getKey());
});

it('does not override an explicitly provided pipeline', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    Pipeline::factory()->for($project)->create(['created_at' => now()->subDays(2)]);
    $chosenPipeline = Pipeline::factory()->for($project)->create(['created_at' => now()->subDay()]);

    $milestone = $project->milestones()->create([
        'title' => 'Beta',
        'description' => 'Beta milestone',
        'pipeline_id' => $chosenPipeline->getKey(),
    ]);

    expect($milestone->pipeline_id)->toBe($chosenPipeline->getKey());
});

it('leaves the pipeline null when the project has no active pipeline', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $milestone = $project->milestones()->create([
        'title' => 'Orphan',
        'description' => 'No pipelines yet',
    ]);

    expect($milestone->pipeline_id)->toBeNull();
});

it('rejects an explicit pipeline that belongs to another project', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $otherProject = Project::factory()->create();

    $foreignPipeline = Pipeline::factory()->for($otherProject)->create();

    expect(fn () => $project->milestones()->create([
        'title' => 'Cross Project',
        'description' => 'Should be rejected',
        'pipeline_id' => $foreignPipeline->getKey(),
    ]))->toThrow(InvalidArgumentException::class);

    expect(ProjectMilestone::query()->where('title', 'Cross Project')->exists())->toBeFalse();
});

it('breaks ties deterministically by id when pipelines share a created_at', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $sharedCreatedAt = now()->subDay();

    $pipelines = collect([
        Pipeline::factory()->for($project)->create(['created_at' => $sharedCreatedAt]),
        Pipeline::factory()->for($project)->create(['created_at' => $sharedCreatedAt]),
    ]);

    $expectedPipelineId = $pipelines->sortBy('id')->first()->getKey();

    $milestone = $project->milestones()->create([
        'title' => 'Same Second',
        'description' => 'Tie-breaker check',
    ]);

    expect($milestone->pipeline_id)->toBe($expectedPipelineId);
});
