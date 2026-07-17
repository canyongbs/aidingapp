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
use AidingApp\Project\Notifications\PipelineEntryAssignedToUserNotification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

/**
 * Returns base data required for all kanban addEntry submissions.
 *
 * @return array<string, mixed>
 */
function baseKanbanEntryData(string $name = 'Test Entry'): array
{
    $contact = Contact::factory()->create();

    return [
        'name' => $name,
        'organizable_type' => $contact->getMorphClass(),
        'organizable_id' => $contact->id,
    ];
}

it('can render create pipeline entry with proper permission', function () {
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
    $user->refresh();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->assertActionVisible(TestAction::make('addEntry')->table());
});

it('can create a pipeline entry via the kanban add entry action', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $stage = $pipeline->stages->first();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('addEntry', data: [
            ...baseKanbanEntryData('Kanban Entry'),
        ], arguments: ['stage' => $stage->id])
        ->assertHasNoActionErrors();

    assertDatabaseHas(PipelineEntry::class, [
        'name' => 'Kanban Entry',
        'pipeline_stage_id' => $stage->id,
    ]);
});

it('can create a kanban entry with description and due date', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $stage = $pipeline->stages->first();
    $due = now()->addDays(5)->toDateTimeString();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('addEntry', data: [
            ...baseKanbanEntryData('Entry With Details'),
            'description' => 'Kanban description.',
            'due' => $due,
        ], arguments: ['stage' => $stage->id])
        ->assertHasNoActionErrors();

    assertDatabaseHas(PipelineEntry::class, [
        'name' => 'Entry With Details',
        'description' => 'Kanban description.',
        'pipeline_stage_id' => $stage->id,
    ]);
});

it('can create a kanban entry with an assigned user', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $stage = $pipeline->stages->first();
    $user = User::factory()->create();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('addEntry', data: [
            ...baseKanbanEntryData('Assigned Kanban Entry'),
            'assigned_to_type' => 'user',
            'assigned_to_id' => $user->id,
        ], arguments: ['stage' => $stage->id])
        ->assertHasNoActionErrors();

    assertDatabaseHas(PipelineEntry::class, [
        'name' => 'Assigned Kanban Entry',
        'assigned_to_id' => $user->id,
        'pipeline_stage_id' => $stage->id,
    ]);
});

it('can create a kanban entry with an assigned contact', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $stage = $pipeline->stages->first();
    $contact = Contact::factory()->create();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('addEntry', data: [
            ...baseKanbanEntryData('Assigned Contact Kanban Entry'),
            'assigned_to_type' => 'contact',
            'assigned_to_id' => $contact->id,
        ], arguments: ['stage' => $stage->id])
        ->assertHasNoActionErrors();

    assertDatabaseHas(PipelineEntry::class, [
        'name' => 'Assigned Contact Kanban Entry',
        'assigned_to_type' => 'contact',
        'assigned_to_id' => $contact->id,
        'pipeline_stage_id' => $stage->id,
    ]);
});

it('can create a kanban entry with no assigned user', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $stage = $pipeline->stages->first();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('addEntry', data: [
            ...baseKanbanEntryData('Unassigned Kanban Entry'),
            'assigned_to_type' => null,
            'assigned_to_id' => null,
        ], arguments: ['stage' => $stage->id])
        ->assertHasNoActionErrors();

    assertDatabaseHas(PipelineEntry::class, [
        'name' => 'Unassigned Kanban Entry',
        'assigned_to_id' => null,
        'pipeline_stage_id' => $stage->id,
    ]);
});

it('persists related milestones, assets, and service requests via the kanban add entry action', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $stage = $pipeline->stages->first();

    $milestone = ProjectMilestone::factory()->create(['project_id' => $project->id]);
    $asset = Asset::factory()->create();
    $serviceRequest = ServiceRequest::factory()->create();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('addEntry', data: [
            ...baseKanbanEntryData('Related Kanban Entry'),
            'milestones' => [$milestone->id],
            'assets' => [$asset->id],
            'serviceRequests' => [$serviceRequest->id],
        ], arguments: ['stage' => $stage->id])
        ->assertHasNoActionErrors();

    $entry = PipelineEntry::where('name', 'Related Kanban Entry')->sole();

    expect($entry->milestones->pluck('id')->all())->toBe([$milestone->id]);
    expect($entry->assets->pluck('id')->all())->toBe([$asset->id]);
    expect($entry->serviceRequests->pluck('id')->all())->toBe([$serviceRequest->id]);
});

it('requires a name when creating a kanban entry', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $stage = $pipeline->stages->first();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('addEntry', data: [
            ...baseKanbanEntryData(),
            'name' => null,
        ], arguments: ['stage' => $stage->id])
        ->assertHasActionErrors(['name' => 'required']);
});

it('validates assigned_to_id must be a valid user id in the kanban action', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $stage = $pipeline->stages->first();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('addEntry', data: [
            ...baseKanbanEntryData('Bad User'),
            'assigned_to_type' => 'user',
            'assigned_to_id' => (string) str()->uuid(),
        ], arguments: ['stage' => $stage->id])
        ->assertHasActionErrors(['assigned_to_id']);
});

it('validates assigned_to_id must be a valid contact id in the kanban action', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $stage = $pipeline->stages->first();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('addEntry', data: [
            ...baseKanbanEntryData('Bad Contact'),
            'assigned_to_type' => 'contact',
            'assigned_to_id' => (string) str()->uuid(),
        ], arguments: ['stage' => $stage->id])
        ->assertHasActionErrors(['assigned_to_id']);
});

it('sends notification when creating a kanban entry with an assigned user', function () {
    Notification::fake();

    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $stage = $pipeline->stages->first();
    $user = User::factory()->create();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('addEntry', data: [
            ...baseKanbanEntryData('Notified Entry'),
            'assigned_to_type' => 'user',
            'assigned_to_id' => $user->id,
        ], arguments: ['stage' => $stage->id])
        ->assertHasNoActionErrors();

    Notification::assertSentTo($user, PipelineEntryAssignedToUserNotification::class);
});

it('does not send notification when creating a kanban entry with no assigned user', function () {
    Notification::fake();

    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $stage = $pipeline->stages->first();

    livewire(PipelineEntryKanban::class, ['pipeline' => $pipeline])
        ->callAction('addEntry', data: [
            ...baseKanbanEntryData('Silent Entry'),
            'assigned_to_type' => null,
            'assigned_to_id' => null,
        ], arguments: ['stage' => $stage->id])
        ->assertHasNoActionErrors();

    Notification::assertNothingSent();
});
