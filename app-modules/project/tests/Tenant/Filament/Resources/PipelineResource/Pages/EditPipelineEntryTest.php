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
use AidingApp\Project\Filament\Resources\Pipelines\Pages\EditPipelineEntry;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectMilestone;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render edit pipeline entry with proper permission', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
    ]);

    livewire(EditPipelineEntry::class, [
        'record' => $pipeline,
        'pipelineEntry' => $entry,
    ])
        ->assertForbidden();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('pipeline.view-any');
    $user->givePermissionTo('pipeline.*.update');
    $user->refresh();

    livewire(EditPipelineEntry::class, [
        'record' => $pipeline,
        'pipelineEntry' => $entry,
    ])
        ->assertSuccessful();
});

it('returns 404 if pipeline entry does not belong to the pipeline', function () {
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
        'pipeline_stage_id' => $otherPipeline->stages->first()->id,
    ]);

    livewire(EditPipelineEntry::class, [
        'record' => $pipeline,
        'pipelineEntry' => $entry,
    ])
        ->assertStatus(404);
});

it('can save pipeline entry with updated description and due date', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'description' => 'Original description.',
    ]);

    $due = now()->addDays(14)->toDateTimeString();

    livewire(EditPipelineEntry::class, [
        'record' => $pipeline,
        'pipelineEntry' => $entry,
    ])
        ->fillForm([
            'description' => 'Updated description.',
            'due' => $due,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(PipelineEntry::class, [
        'id' => $entry->id,
        'description' => 'Updated description.',
    ]);
});

it('persists related milestones, assets, and service requests on save', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
    ]);

    $milestone = ProjectMilestone::factory()->create(['project_id' => $project->id]);
    $asset = Asset::factory()->create();
    $serviceRequest = ServiceRequest::factory()->create();

    livewire(EditPipelineEntry::class, [
        'record' => $pipeline,
        'pipelineEntry' => $entry,
    ])
        ->fillForm([
            'milestones' => [$milestone->id],
            'assets' => [$asset->id],
            'serviceRequests' => [$serviceRequest->id],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $entry->refresh();

    expect($entry->milestones->pluck('id')->all())->toBe([$milestone->id]);
    expect($entry->assets->pluck('id')->all())->toBe([$asset->id]);
    expect($entry->serviceRequests->pluck('id')->all())->toBe([$serviceRequest->id]);
});

it('can save pipeline entry with an assigned user', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'assigned_to_type' => null,
        'assigned_to_id' => null,
    ]);

    $user = User::factory()->create();

    livewire(EditPipelineEntry::class, [
        'record' => $pipeline,
        'pipelineEntry' => $entry,
    ])
        ->fillForm([
            'assigned_to_type' => 'user',
            'assigned_to_id' => $user->id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(PipelineEntry::class, [
        'id' => $entry->id,
        'assigned_to_id' => $user->id,
    ]);
});

it('can save pipeline entry with an assigned contact', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'assigned_to_type' => null,
        'assigned_to_id' => null,
    ]);

    $contact = Contact::factory()->create();

    livewire(EditPipelineEntry::class, [
        'record' => $pipeline,
        'pipelineEntry' => $entry,
    ])
        ->fillForm([
            'assigned_to_type' => 'contact',
            'assigned_to_id' => $contact->id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(PipelineEntry::class, [
        'id' => $entry->id,
        'assigned_to_type' => 'contact',
        'assigned_to_id' => $contact->id,
    ]);
});

it('can clear the assigned user on a pipeline entry', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'assigned_to_type' => 'user',
        'assigned_to_id' => User::factory()->create()->id,
    ]);

    livewire(EditPipelineEntry::class, [
        'record' => $pipeline,
        'pipelineEntry' => $entry,
    ])
        ->fillForm([
            'assigned_to_type' => null,
            'assigned_to_id' => null,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(PipelineEntry::class, [
        'id' => $entry->id,
        'assigned_to_id' => null,
    ]);
});

it('sets assigned_to_type to contact when entry has an assigned contact', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $contact = Contact::factory()->create();
    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'assigned_to_type' => 'contact',
        'assigned_to_id' => $contact->id,
    ]);

    livewire(EditPipelineEntry::class, [
        'record' => $pipeline,
        'pipelineEntry' => $entry,
    ])
        ->assertFormFieldExists('assigned_to_type')
        ->assertSet('data.assigned_to_type', 'contact');
});

it('sets assigned_to_type to user when entry has an assigned user', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $user = User::factory()->create();
    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'assigned_to_type' => 'user',
        'assigned_to_id' => $user->id,
    ]);

    livewire(EditPipelineEntry::class, [
        'record' => $pipeline,
        'pipelineEntry' => $entry,
    ])
        ->assertFormFieldExists('assigned_to_type')
        ->assertSet('data.assigned_to_type', 'user');
});

it('sets assigned_to_type to null when entry has no assigned user', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'assigned_to_type' => null,
        'assigned_to_id' => null,
    ]);

    livewire(EditPipelineEntry::class, [
        'record' => $pipeline,
        'pipelineEntry' => $entry,
    ])
        ->assertSet('data.assigned_to_type', null);
});
