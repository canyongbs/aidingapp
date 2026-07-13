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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectMilestone;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

use function Tests\asSuperAdmin;


it('can assign a pipeline entry to a user', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'assigned_to_type' => $user->getMorphClass(),
        'assigned_to_id' => $user->id,
    ]);

    expect($entry->assignedTo)->toBeInstanceOf(User::class);
    expect($entry->assignedTo->id)->toBe($user->id);
    expect($entry->assigned_to_type)->toBe('user');
});

it('can assign a pipeline entry to a contact', function () {
    asSuperAdmin();

    $contact = Contact::factory()->create();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'assigned_to_type' => $contact->getMorphClass(),
        'assigned_to_id' => $contact->id,
    ]);

    expect($entry->assignedTo)->toBeInstanceOf(Contact::class);
    expect($entry->assignedTo->id)->toBe($contact->id);
    expect($entry->assigned_to_type)->toBe('contact');
});

it('can have related milestones', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $milestones = ProjectMilestone::factory()->count(3)->create([
        'project_id' => $project->id,
    ]);

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
    ]);

    $entry->milestones()->sync($milestones->pluck('id'));

    expect($entry->milestones)->toHaveCount(3);
    expect($entry->milestones->pluck('id')->sort()->values()->toArray())
        ->toBe($milestones->pluck('id')->sort()->values()->toArray());
});

it('can have related assets', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $assets = Asset::factory()->count(2)->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
    ]);

    $entry->assets()->sync($assets->pluck('id'));

    expect($entry->assets)->toHaveCount(2);
    expect($entry->assets->pluck('id')->sort()->values()->toArray())
        ->toBe($assets->pluck('id')->sort()->values()->toArray());
});

it('can have related service requests', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $serviceRequests = ServiceRequest::factory()->count(2)->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
    ]);

    $entry->serviceRequests()->sync($serviceRequests->pluck('id'));

    expect($entry->serviceRequests)->toHaveCount(2);
    expect($entry->serviceRequests->pluck('id')->sort()->values()->toArray())
        ->toBe($serviceRequests->pluck('id')->sort()->values()->toArray());
});

it('defaults is_visible_to_guests to true', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
    ]);

    expect($entry->is_visible_to_guests)->toBeTrue();
});

it('can set is_visible_to_guests to false', function () {
    asSuperAdmin();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'is_visible_to_guests' => false,
    ]);

    expect($entry->is_visible_to_guests)->toBeFalse();
});

it('can sync and detach milestones', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    $milestones = ProjectMilestone::factory()->count(3)->create([
        'project_id' => $project->id,
    ]);

    $entry = PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
    ]);

    $entry->milestones()->sync($milestones->pluck('id'));
    expect($entry->fresh()->milestones)->toHaveCount(3);

    $entry->milestones()->sync($milestones->take(1)->pluck('id'));
    expect($entry->fresh()->milestones)->toHaveCount(1);

    $entry->milestones()->detach();
    expect($entry->fresh()->milestones)->toHaveCount(0);
});

it('does not send notification when assigned to a contact', function () {
    asSuperAdmin();

    $contact = Contact::factory()->create();

    $pipeline = Pipeline::factory()
        ->for(Project::factory()->create())
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    Notification::fake();

    PipelineEntry::factory()->create([
        'pipeline_stage_id' => $pipeline->stages->first()->id,
        'assigned_to_type' => $contact->getMorphClass(),
        'assigned_to_id' => $contact->id,
    ]);

    Notification::assertNothingSent();
});
