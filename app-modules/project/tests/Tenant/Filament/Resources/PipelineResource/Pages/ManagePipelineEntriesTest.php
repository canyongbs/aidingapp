<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\Contact\Models\Contact;
use AidingApp\Project\Filament\Resources\PipelineResource\Pages\ManagePipelineEntries;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render with proper permission', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(2), 'stages')
        ->create();

    get(ManagePipelineEntries::getUrl([
        'record' => $pipeline->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('pipeline.view-any');
    $user->refresh();

    get(ManagePipelineEntries::getUrl([
        'record' => $pipeline->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can list pipeline entries', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(3), 'stages')
        ->create();

    $entries = PipelineEntry::factory()
        ->count(5)
        ->create([
            'pipeline_stage_id' => $pipeline->stages->first()->id,
        ]);

    livewire(ManagePipelineEntries::class, [
        'record' => $pipeline->getRouteKey(),
    ])
        ->assertCanSeeTableRecords($entries);
});

it('can filter pipeline entries by stage', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(3), 'stages')
        ->create();

    $stage1 = $pipeline->stages->first();
    $stage2 = $pipeline->stages->skip(1)->first();

    $entriesStage1 = PipelineEntry::factory()
        ->count(3)
        ->create(['pipeline_stage_id' => $stage1->id]);

    $entriesStage2 = PipelineEntry::factory()
        ->count(2)
        ->create(['pipeline_stage_id' => $stage2->id]);

    livewire(ManagePipelineEntries::class, [
        'record' => $pipeline->getRouteKey(),
    ])
        ->filterTable('stage', $stage1->id)
        ->assertCanSeeTableRecords($entriesStage1)
        ->assertCanNotSeeTableRecords($entriesStage2);
});

it('can create pipeline entries', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(2), 'stages')
        ->create();

    $stage = $pipeline->stages->first();

    livewire(ManagePipelineEntries::class, [
        'record' => $pipeline->getKey(),
    ])
        ->callTableAction('create', data: [
            'name' => 'New Entry',
            'pipeline_stage_id' => $stage->id,
            'organizable_type' => Contact::class,
            'organizable_id' => Contact::factory()->create()->id,
        ])
        ->assertHasNoTableActionErrors();

    assertDatabaseHas(
        PipelineEntry::class,
        ['pipeline_stage_id' => $stage->id]
    );
});

it('validates stage_id is required when creating pipeline entries', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(2), 'stages')
        ->create();

    livewire(ManagePipelineEntries::class, [
        'record' => $pipeline->getKey(),
    ])
        ->callTableAction('create', data: [
            'stage_id' => null,
        ])
        ->assertHasTableActionErrors(['pipeline_stage_id' => 'required']);
});

it('can delete pipeline entries', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(2), 'stages')
        ->create();

    $entry = PipelineEntry::factory()
        ->create([
            'pipeline_stage_id' => $pipeline->stages->first()->id,
        ]);

    livewire(ManagePipelineEntries::class, [
        'record' => $pipeline->getKey(),
    ])
        ->callTableAction('delete', record: $entry->getKey())
        ->assertHasNoTableActionErrors();

    assertDatabaseMissing(
        PipelineEntry::class,
        ['id' => $entry->id]
    );
});

it('can switch view types', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(2), 'stages')
        ->create();

    $component = livewire(ManagePipelineEntries::class, [
        'record' => $pipeline->getRouteKey(),
    ]);

    expect($component->viewType)->toBe('table');

    $component->call('setViewType', 'kanban');

    expect($component->viewType)->toBe('kanban');
    expect(session('pipeline-view-type'))->toBe('kanban');
});
