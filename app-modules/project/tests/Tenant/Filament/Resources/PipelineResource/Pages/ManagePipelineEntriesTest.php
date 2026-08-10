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

use AidingApp\Project\Filament\Resources\Pipelines\Pages\ManagePipelineEntries;
use AidingApp\Project\Filament\Resources\Pipelines\PipelineResource;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render with proper permission', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(2), 'stages')
        ->create();

    get(ManagePipelineEntries::getUrl([
        'record' => $pipeline->getRouteKey(),
        'project' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('pipeline.view-any');
    $user->refresh();

    get(ManagePipelineEntries::getUrl([
        'record' => $pipeline->getRouteKey(),
        'project' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('renders the kanban board', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    get(ManagePipelineEntries::getUrl([
        'record' => $pipeline->getRouteKey(),
        'project' => $project->getRouteKey(),
    ]))
        ->assertSuccessful()
        ->assertSee('Drag pipeline task here');
});

it('returns 404 if the project route parameter does not match the pipeline\'s project', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $otherProject = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    get(ManagePipelineEntries::getUrl([
        'record' => $pipeline->getRouteKey(),
        'project' => $otherProject->getRouteKey(),
    ]))
        ->assertNotFound();
});

it('shows a back to project link', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    get(ManagePipelineEntries::getUrl([
        'record' => $pipeline->getRouteKey(),
        'project' => $project->getRouteKey(),
    ]))
        ->assertSuccessful()
        ->assertSee('Back to Project');
});

it('shows the selected pipeline name on the pipeline switcher trigger', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create(['name' => 'Onboarding Pipeline']);

    get(ManagePipelineEntries::getUrl([
        'record' => $pipeline->getRouteKey(),
        'project' => $project->getRouteKey(),
    ]))
        ->assertSuccessful()
        ->assertSee('Onboarding Pipeline');
});

it('can switch to a different pipeline within the same project via the select pipeline action', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipelineOne = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();
    $pipelineTwo = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    livewire(ManagePipelineEntries::class, [
        'record' => $pipelineOne->getRouteKey(),
        'parentRecord' => $project,
    ])
        ->assertActionVisible('selectPipeline')
        ->callAction('selectPipeline', data: ['pipeline_id' => $pipelineTwo->getKey()])
        ->assertRedirect(ManagePipelineEntries::getUrl([
            'record' => $pipelineTwo->getRouteKey(),
            'project' => $project->getRouteKey(),
        ]));
});

it('rejects switching to a pipeline that does not belong to the project', function () {
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

    livewire(ManagePipelineEntries::class, [
        'record' => $pipeline->getRouteKey(),
        'parentRecord' => $project,
    ])
        ->callAction('selectPipeline', data: ['pipeline_id' => $foreignPipeline->getKey()])
        ->assertNotified('Invalid pipeline selection');
});

it('hides the pipeline resource sub navigation', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $component = livewire(ManagePipelineEntries::class, [
        'record' => $pipeline->getRouteKey(),
        'parentRecord' => $project,
    ])->instance();

    expect(PipelineResource::getRecordSubNavigation($component))->toBe([]);
});

it('hides the table view and edit actions without pipeline view and update permissions', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
    ]);

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('pipeline.view-any');
    $user->refresh();

    expect($user->can('view', $pipeline))->toBeFalse()
        ->and($user->can('update', $pipeline))->toBeFalse();

    livewire(ManagePipelineEntries::class, [
        'record' => $pipeline->getRouteKey(),
        'parentRecord' => $project,
    ])
        ->assertTableActionHidden('view', $entry)
        ->assertTableActionHidden('edit', $entry);
});

it('shows the table view action but hides the edit action with only pipeline view permission', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
    ]);

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('pipeline.view-any');
    $user->givePermissionTo('pipeline.*.view');
    $user->refresh();

    expect($user->can('view', $pipeline))->toBeTrue()
        ->and($user->can('update', $pipeline))->toBeFalse();

    livewire(ManagePipelineEntries::class, [
        'record' => $pipeline->getRouteKey(),
        'parentRecord' => $project,
    ])
        ->assertTableActionVisible('view', $entry)
        ->assertTableActionHidden('edit', $entry);
});
