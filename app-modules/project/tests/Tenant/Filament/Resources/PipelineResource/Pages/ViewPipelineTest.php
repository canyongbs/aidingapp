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

use AidingApp\Project\Filament\Resources\Pipelines\Pages\ViewPipeline;
use AidingApp\Project\Filament\Resources\Pipelines\PipelineResource;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;
use function Tests\htmlText;

it('can render with proper permission', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->create();

    get(PipelineResource::getUrl('view', [
        'record' => $pipeline->getRouteKey(),
        'project' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('pipeline.view-any');
    $user->givePermissionTo('pipeline.*.view');

    $user->refresh();

    get(PipelineResource::getUrl('view', [
        'record' => $pipeline->getRouteKey(),
        'project' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can view a pipeline', function (): void {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->create();

    Livewire::test(ViewPipeline::class, [
        'record' => $pipeline->getRouteKey(),
        'parentRecord' => $project,
    ])
        ->assertSuccessful();
});

it('does not resolve a pipeline whose parent project is archived', function (): void {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->create();

    $project->archive();

    get(PipelineResource::getUrl('view', [
        'record' => $pipeline->getRouteKey(),
        'project' => $project->getRouteKey(),
    ]))
        ->assertNotFound();
});

it('displays correct pipeline details', function (): void {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->create([
            'project_id' => $project->id,
            'name' => 'Test Pipeline Name',
            'description' => 'Test Pipeline Description',
        ]);

    Livewire::test(ViewPipeline::class, [
        'record' => $pipeline->getRouteKey(),
        'parentRecord' => $project,
    ])
        ->assertSuccessful()
        ->assertSchemaStateSet([
            'name' => 'Test Pipeline Name',
            'description' => 'Test Pipeline Description',
        ]);
});

it('displays pipeline with multiple stages', function (): void {
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

    $component = Livewire::test(ViewPipeline::class, [
        'record' => $pipeline->getRouteKey(),
        'parentRecord' => $project,
    ])
        ->assertSuccessful()
        ->assertSchemaStateSet([
            'name' => 'Customer Journey',
            'description' => 'Track customer progress',
            'stages' => $pipeline->stages()->get(),
        ]);

    expect(htmlText($component->html(), '//*[contains(concat(" ", normalize-space(@class), " "), " fi-in-entry ")][.//*[@role="term" and normalize-space()="Other stages"]]'))
        ->toContain('Initial Contact', 'Follow Up');
});

it('can access pipeline from project context', function (): void {
    asSuperAdmin();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->for($project)
        ->create();

    expect($pipeline->project->id)->toBe($project->id);

    Livewire::test(ViewPipeline::class, [
        'record' => $pipeline->getRouteKey(),
        'parentRecord' => $project,
    ])
        ->assertSuccessful();
});
