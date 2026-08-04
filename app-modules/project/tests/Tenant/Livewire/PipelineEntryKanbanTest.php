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

use AidingApp\Project\Livewire\PipelineEntryKanban;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('renders entry cards with their name on the kanban board', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'name' => 'Kickoff Task',
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
    ]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->assertSee($entry->name);
});

it('renders stages in order sequence on the kanban board', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->create();

    $third = PipelineStage::factory()->for($pipeline)->create(['name' => 'Third Stage', 'order' => 3]);
    $first = PipelineStage::factory()->for($pipeline)->create(['name' => 'First Stage', 'order' => 1]);
    $second = PipelineStage::factory()->for($pipeline)->create(['name' => 'Second Stage', 'order' => 2]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->assertSeeInOrder([$first->name, $second->name, $third->name]);
});

it('assigns a pipeline entry created from a stage column to that column stage', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(2), 'stages')
        ->create();

    [$firstStage, $secondStage] = $pipeline->stages;

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('addEntry', data: [
            'name' => 'Second Column Entry',
        ], arguments: ['stage' => $secondStage->getKey()])
        ->assertHasNoActionErrors();

    $entry = PipelineEntry::query()->where('name', 'Second Column Entry')->sole();

    expect($entry->pipeline_stage_id)->toBe($secondStage->getKey())
        ->and($entry->pipeline_stage_id)->not->toBe($firstStage->getKey());
});

it('hides the add entry action without pipeline update permission', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('pipeline.view-any');
    $user->givePermissionTo('pipeline.*.view');
    $user->refresh();

    expect($user->can('update', $pipeline))->toBeFalse();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->assertActionHidden('addEntry')
        ->assertDontSeeHtml('addEntry');
});

it('rejects adding a pipeline entry into a stage that does not belong to the pipeline', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $otherStage = PipelineStage::factory()->create();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('addEntry', data: [
            'name' => 'Invalid Stage Entry',
        ], arguments: ['stage' => $otherStage->getKey()])
        ->assertNotified('Pipeline entry could not be added');

    expect(PipelineEntry::query()->where('name', 'Invalid Stage Entry')->exists())->toBeFalse();
});

it('can move a pipeline entry between stages belonging to the pipeline', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(2), 'stages')
        ->create();

    [$fromStage, $toStage] = $pipeline->stages;

    $entry = PipelineEntry::factory()->create(['pipeline_stage_id' => $fromStage->getKey()]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->call('movedEntry', $pipeline->getKey(), $entry->getKey(), $fromStage->getKey(), $toStage->getKey());

    expect($entry->fresh()->pipeline_stage_id)->toBe($toStage->getKey());
});

it('denies moving a pipeline entry without pipeline update permission', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(2), 'stages')
        ->create();

    [$fromStage, $toStage] = $pipeline->stages;

    $entry = PipelineEntry::factory()->create(['pipeline_stage_id' => $fromStage->getKey()]);

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('pipeline.view-any');
    $user->givePermissionTo('pipeline.*.view');
    $user->refresh();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->call('movedEntry', $pipeline->getKey(), $entry->getKey(), $fromStage->getKey(), $toStage->getKey())
        ->assertForbidden();

    expect($entry->fresh()->pipeline_stage_id)->toBe($fromStage->getKey());
});

it('rejects moving a pipeline entry into a stage that does not belong to the pipeline', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $fromStage = $pipeline->stages->first();
    $otherStage = PipelineStage::factory()->create();

    $entry = PipelineEntry::factory()->create(['pipeline_stage_id' => $fromStage->getKey()]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->call('movedEntry', $pipeline->getKey(), $entry->getKey(), $fromStage->getKey(), $otherStage->getKey());

    expect($entry->fresh()->pipeline_stage_id)->toBe($fromStage->getKey());
});

it('rejects moving a pipeline entry that does not belong to the pipeline', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $toStage = $pipeline->stages->first();

    $otherEntry = PipelineEntry::factory()->create();
    $originalStageId = $otherEntry->pipeline_stage_id;

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->call('movedEntry', $pipeline->getKey(), $otherEntry->getKey(), $originalStageId, $toStage->getKey());

    expect($otherEntry->fresh()->pipeline_stage_id)->toBe($originalStageId);
});
