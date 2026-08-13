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
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\Project\Livewire\PipelineEntryKanban;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectMilestone;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertModelMissing;
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

it('renders stage name with card count in the stage header', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(2), 'stages')
        ->create();

    [$firstStage, $secondStage] = $pipeline->stages;

    PipelineEntry::factory()->count(2)->create([
        'pipeline_stage_id' => $firstStage->getKey(),
    ]);

    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $secondStage->getKey(),
    ]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->assertSee($firstStage->name)
        ->assertSee($secondStage->name)
        ->assertSee('2')
        ->assertSee('1');
});

it('renders modern card metadata with assignment and due tooltip', function () {
    asSuperAdmin();

    Carbon::setTestNow('2026-08-07 12:00:00');

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $assignee = User::factory()->create([
        'name' => 'Joe Licata',
    ]);

    $due = now()->addDay()->addHours(3);

    PipelineEntry::factory()->create([
        'name' => 'Modern Card Task',
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
        'assigned_to_type' => $assignee->getMorphClass(),
        'assigned_to_id' => $assignee->getKey(),
        'due' => $due,
    ]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->assertSee('Modern Card Task')
        ->assertSee('Milestones:')
        ->assertSee('Assets:')
        ->assertSee('Service Requests:')
        ->assertSee('Assigned:')
        ->assertSee('Joe Licata (User)')
        ->assertSee('Due:')
        ->assertSee('1 Day 3 Hours')
        ->assertSee($due->format('M j, Y g:i A'));

    Carbon::setTestNow();
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
        ->assertNotified('Pipeline task could not be added');

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

it('can view a pipeline entry through the slide over modal', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $contact = Contact::factory()->create();

    $entry = PipelineEntry::factory()->create([
        'name' => 'View Modal Task',
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
        'assigned_to_type' => $contact->getMorphClass(),
        'assigned_to_id' => $contact->getKey(),
    ]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->mountAction('viewPipelineEntry', ['entry' => $entry->getKey()])
        ->assertActionMounted('viewPipelineEntry')
        ->assertSchemaStateSet([
            'name' => 'View Modal Task',
            'assignedTo' => $contact->full_name,
            'pipelineStage.name' => $pipeline->stages->first()->name,
        ]);
});

it('hides the view action without pipeline view permission', function () {
    $user = User::factory()->create();

    actingAs($user);

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
    ]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->assertActionHidden('viewPipelineEntry');
});

it('can edit a pipeline entry through the slide over modal', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
        'description' => 'Original description.',
    ]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->mountAction('editPipelineEntry', ['entry' => $entry->getKey()])
        ->assertActionMounted('editPipelineEntry')
        ->setActionData([
            'description' => 'Updated description.',
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    assertDatabaseHas(PipelineEntry::class, [
        'id' => $entry->getKey(),
        'description' => 'Updated description.',
    ]);
});

it('persists related milestones, assets, and service requests when edited through the modal', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
    ]);

    $milestone = ProjectMilestone::factory()->create(['project_id' => $project->getKey()]);
    $asset = Asset::factory()->create();
    $serviceRequest = ServiceRequest::factory()->create();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->mountAction('editPipelineEntry', ['entry' => $entry->getKey()])
        ->setActionData([
            'milestones' => [$milestone->getKey()],
            'assets' => [$asset->getKey()],
            'serviceRequests' => [$serviceRequest->getKey()],
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    $entry->refresh();

    expect($entry->milestones->pluck('id')->all())->toBe([$milestone->getKey()])
        ->and($entry->assets->pluck('id')->all())->toBe([$asset->getKey()])
        ->and($entry->serviceRequests->pluck('id')->all())->toBe([$serviceRequest->getKey()]);
});

it('fails to edit a pipeline entry that belongs to a different pipeline', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $otherPipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $otherPipeline->stages->first()->getKey(),
    ]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->mountAction('editPipelineEntry', ['entry' => $entry->getKey()]);
})->throws(ModelNotFoundException::class);

it('preserves related milestones, assets, and service requests when edited without changing them', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
    ]);

    $milestone = ProjectMilestone::factory()->create(['project_id' => $project->getKey()]);
    $asset = Asset::factory()->create();
    $serviceRequest = ServiceRequest::factory()->create();

    $entry->milestones()->sync([$milestone->getKey()]);
    $entry->assets()->sync([$asset->getKey()]);
    $entry->serviceRequests()->sync([$serviceRequest->getKey()]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->mountAction('editPipelineEntry', ['entry' => $entry->getKey()])
        ->assertActionMounted('editPipelineEntry')
        ->assertActionDataSet([
            'milestones_type' => 'select',
            'assets_type' => 'select',
            'service_requests_type' => 'select',
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    $entry->refresh();

    expect($entry->milestones->pluck('id')->all())->toBe([$milestone->getKey()])
        ->and($entry->assets->pluck('id')->all())->toBe([$asset->getKey()])
        ->and($entry->serviceRequests->pluck('id')->all())->toBe([$serviceRequest->getKey()]);
});

it('clears related milestones, assets, and service requests when the type is set to none', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
    ]);

    $milestone = ProjectMilestone::factory()->create(['project_id' => $project->getKey()]);
    $asset = Asset::factory()->create();
    $serviceRequest = ServiceRequest::factory()->create();

    $entry->milestones()->sync([$milestone->getKey()]);
    $entry->assets()->sync([$asset->getKey()]);
    $entry->serviceRequests()->sync([$serviceRequest->getKey()]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->mountAction('editPipelineEntry', ['entry' => $entry->getKey()])
        ->assertActionMounted('editPipelineEntry')
        ->assertActionDataSet([
            'milestones_type' => 'select',
            'assets_type' => 'select',
            'service_requests_type' => 'select',
        ])
        ->setActionData([
            'milestones_type' => 'none',
            'assets_type' => 'none',
            'service_requests_type' => 'none',
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    $entry->refresh();

    expect($entry->milestones->pluck('id')->all())->toBe([])
        ->and($entry->assets->pluck('id')->all())->toBe([])
        ->and($entry->serviceRequests->pluck('id')->all())->toBe([]);
});

it('can remove a pipeline entry through the dropdown', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
    ]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('removePipelineEntry', arguments: ['entry' => $entry->getKey()])
        ->assertHasNoActionErrors()
        ->assertNotified('Pipeline task removed successfully');

    assertModelMissing($entry);
});

it('fails to remove a pipeline entry that belongs to a different pipeline', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $otherPipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $otherPipeline->stages->first()->getKey(),
    ]);

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('removePipelineEntry', arguments: ['entry' => $entry->getKey()]);
})->throws(ModelNotFoundException::class);

it('hides the remove action without pipeline update permission', function () {
    $user = User::factory()->create();

    actingAs($user);

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
    ]);

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('pipeline.view-any');
    $user->givePermissionTo('pipeline.*.view');
    $user->refresh();

    expect($user->can('update', $pipeline))->toBeFalse();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->assertActionHidden('removePipelineEntry');
});

it('hides the edit action without pipeline update permission', function () {
    $user = User::factory()->create();

    actingAs($user);

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
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

    expect($user->can('update', $pipeline))->toBeFalse();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->assertActionVisible('viewPipelineEntry')
        ->assertActionHidden('editPipelineEntry');
});

it('shows the view and edit actions with update permission on the pipeline\'s project', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();
    $project->managerUsers()->attach($user);

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->getKey(),
    ]);

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('project.*.update');
    $user->givePermissionTo('pipeline.view-any');
    $user->refresh();

    expect($user->can('view', $pipeline))->toBeTrue()
        ->and($user->can('update', $pipeline))->toBeTrue();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->assertActionVisible('viewPipelineEntry')
        ->assertActionVisible('editPipelineEntry');
});
