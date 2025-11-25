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
use AidingApp\Project\Filament\Resources\ProjectResource\Pages\ManagePipelines;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Tests\Tenant\Pipeline\RequestFactories\CreatePipelineRequestFactory;
use AidingApp\Project\Tests\Tenant\Pipeline\RequestFactories\EditPipelineRequestFactory;
use App\Models\User;
use Filament\Forms\Components\Repeater;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

it('can render with proper permission.', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $project = Project::factory()->create();

    get(ManagePipelines::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->refresh();

    get(ManagePipelines::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can list pipelines', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $project = Project::factory()->create();

    $pipelines = Pipeline::factory()
        ->has(PipelineStage::factory()->count(3), 'stages')
        ->for($project)
        ->state(['user_id' => $superAdmin->id])
        ->count(2)
        ->create();

    livewire(ManagePipelines::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertCanSeeTableRecords($pipelines);
});

it('can validate create pipeline inputs', function ($data, $errors) {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()->make($data);

    livewire(ManagePipelines::class, [
        'record' => $project->getKey(),
    ])
        ->callTableAction('create', data: $pipeline->toArray())
        ->assertHasTableActionErrors([$errors]);

    $pipelineData = collect($pipeline->toArray())->toArray();

    assertDatabaseMissing(
        Pipeline::class,
        $pipelineData
    );
})->with([
    '`name` is required' => [['name' => null], 'name', 'The name field is required.'],
    '`name` is max 255 characters' => [['name' => str_repeat('a', 256)], 'name', 'The name may not be greater than 255 characters.'],
    '`description` is required' => [['description' => null], 'description', 'The description field is required.'],
    '`description` is max 65535 characters' => [['description' => str_repeat('a', 65536)], 'description', 'The description may not be greater than 65535 characters.'],
    '`default_stage` is required' => [['default_stage' => null], 'default_stage', 'The default pipeline stage name field is required.'],
]);

it('can create pipelines', function () {
    $undoRepeaterFake = Repeater::fake();

    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $project = Project::factory()->create();

    $pipelineData = CreatePipelineRequestFactory::new()->create();

    livewire(ManagePipelines::class, [
        'record' => $project->getRouteKey(),
    ])
        ->callTableAction('create', data: $pipelineData)
        ->assertHasNoTableActionErrors();

    $createdPipeline = Pipeline::first();
    assertCount(1, Pipeline::all());
    expect($createdPipeline->stages)->toHaveCount(3);

    $undoRepeaterFake();
});

it('can edit pipelines', function () {
    $undoRepeaterFake = Repeater::fake();

    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->has(PipelineStage::factory()->count(3), 'stages')
        ->for($project)
        ->state([
            'name' => 'Test Pipeline',
            'description' => 'Test pipeline description',
            'default_stage' => 'Initial Stage',
            'user_id' => $superAdmin->id,
        ])->create();

    $requestData = EditPipelineRequestFactory::new()->create();

    livewire(ManagePipelines::class, [
        'record' => $project->getKey(),
    ])
        ->callTableAction('edit', record: $pipeline->getKey(), data: $requestData)
        ->assertHasNoTableActionErrors();

    $pipeline->refresh();

    assertDatabaseHas(
        Pipeline::class,
        [
            'id' => $pipeline->id,
            'name' => $requestData['name'],
            'description' => $requestData['description'],
            'default_stage' => $requestData['default_stage'],
        ]
    );

    $undoRepeaterFake();
});

it('can delete pipelines', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->has(PipelineStage::factory()->count(3), 'stages')
        ->for($project)
        ->state(['user_id' => $superAdmin->id])
        ->create();

    livewire(ManagePipelines::class, [
        'record' => $project->getKey(),
    ])
        ->callTableAction('delete', record: $pipeline->getKey())
        ->assertHasNoTableActionErrors();

    assertDatabaseMissing(
        Pipeline::class,
        ['id' => $pipeline->id]
    );
});

it('deletes pipeline stages when pipeline is deleted', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->has(PipelineStage::factory()->count(2), 'stages')
        ->for($project)
        ->state(['user_id' => $superAdmin->id])
        ->create();

    $stageIds = $pipeline->stages->pluck('id')->toArray();

    livewire(ManagePipelines::class, [
        'record' => $project->getKey(),
    ])
        ->callTableAction('delete', record: $pipeline->getKey())
        ->assertHasNoTableActionErrors();

    assertDatabaseMissing('pipeline_stages', ['id' => $stageIds[0]]);
    assertDatabaseMissing('pipeline_stages', ['id' => $stageIds[1]]);
});
