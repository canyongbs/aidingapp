<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
use AidingApp\Project\Filament\Resources\PipelineResource;
use AidingApp\Project\Filament\Resources\PipelineResource\Pages\ViewPipeline;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render with proper permission', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->create();

    get(PipelineResource::getUrl('view', [
        'record' => $pipeline->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('pipeline.view-any');
    $user->givePermissionTo('pipeline.*.view');

    $user->refresh();

    get(PipelineResource::getUrl('view', [
        'record' => $pipeline->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can view a pipeline', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->create();

    livewire(ViewPipeline::class, [
        'record' => $pipeline->getRouteKey(),
    ])
        ->assertSuccessful();
});

it('displays correct pipeline details', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->create([
            'project_id' => $project->id,
            'name' => 'Test Pipeline Name',
            'description' => 'Test Pipeline Description',
        ]);

    livewire(ViewPipeline::class, [
        'record' => $pipeline->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertSee('Test Pipeline Name')
        ->assertSee('Test Pipeline Description');
});

it('displays pipeline with multiple stages', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->create([
            'name' => 'Customer Journey',
            'description' => 'Track customer progress',
        ]);

    PipelineStage::factory()->create([
        'pipeline_id' => $pipeline->id,
        'name' => 'Initial Contact',
        'order' => 1,
    ]);

    PipelineStage::factory()->create([
        'pipeline_id' => $pipeline->id,
        'name' => 'Follow Up',
        'order' => 2,
    ]);

    livewire(ViewPipeline::class, [
        'record' => $pipeline->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertSee('Customer Journey')
        ->assertSee('Track customer progress')
        ->assertSee('Initial Contact')
        ->assertSee('Follow Up');
});

it('can access pipeline from project context', function () {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->create();

    expect($pipeline->project->id)->toBe($project->id);

    livewire(ViewPipeline::class, [
        'record' => $pipeline->getRouteKey(),
    ])
        ->assertSuccessful();
});
