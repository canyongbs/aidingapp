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
use AidingApp\Project\Filament\Resources\PipelineResource\Pages\EditPipeline;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Tests\Tenant\Filament\Resources\PipelineResource\RequestFactory\EditPipelineRequestFactory;
use App\Models\User;
use Filament\Forms\Components\Repeater;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render with proper permission.', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $project = Project::factory()->create();
     $pipeline = Pipeline::factory()
        ->has(PipelineStage::factory()->count(3), 'stages')
        ->for($project)
        ->state([
            'name' => 'Test Pipeline',
            'description' => 'Test pipeline description',
        ])->create();

    livewire(EditPipeline::class, [
        'record' => $pipeline->getKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('pipeline.view-any');
    $user->givePermissionTo('project.*.update');
    $user->givePermissionTo('pipeline.*.update');
    $user->refresh();

    livewire(EditPipeline::class, [
        'record' => $pipeline->getKey(),
    ])
        ->assertSuccessful();
});

it('can validate edit pipeline inputs', function ($data, $errors) {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $request = EditPipelineRequestFactory::new($data)->create();

    livewire(EditPipeline::class)
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [
        EditPipelineRequestFactory::new()->without('name'),
        ['name' => 'required'],
    ],
    'name max' => [
        EditPipelineRequestFactory::new()->state(['name' => str()->random(256)]),
        ['name' => 'max'],
    ],
    'description required' => [
        EditPipelineRequestFactory::new()->without('description'),
        ['description' => 'required'],
    ],
    'description max' => [
        EditPipelineRequestFactory::new()->state(['description' => str()->random(65536)]),
        ['description' => 'max'],
    ],
]);


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
        ])->create();

    $requestData = EditPipelineRequestFactory::new()->create();

    livewire(EditPipeline::class, [
        'record' => $pipeline->getKey(),
    ])
        ->fillForm($requestData)
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertHasNoErrors();

    $pipeline->refresh();

    assertDatabaseHas(
        Pipeline::class,
        [
            'id' => $pipeline->id,
            'name' => $requestData['name'],
            'description' => $requestData['description'],
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
        ->create();

    livewire(EditPipeline::class, [
        'record' => $pipeline->getKey(),
    ])
        ->callAction('delete')
        ->assertRedirect();
});
