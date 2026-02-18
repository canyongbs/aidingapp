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

use AidingApp\Project\Filament\Resources\PipelineResource\Pages\CreatePipeline;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Tests\Tenant\Filament\Resources\PipelineResource\RequestFactory\CreatePipelineRequestFactory;
use App\Models\User;
use Filament\Forms\Components\Repeater;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

it('can render with proper permission.', function () {
    $user = User::factory()->create();

    actingAs($user);

    livewire(CreatePipeline::class)
        ->assertForbidden();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('pipeline.view-any');
    $user->givePermissionTo('pipeline.create');
    $user->refresh();

    livewire(CreatePipeline::class)
        ->assertSuccessful();
});

it('can validate create pipeline inputs', function ($data, $errors) {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $request = CreatePipelineRequestFactory::new($data)->create();

    livewire(CreatePipeline::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [
        CreatePipelineRequestFactory::new()->without('name'),
        ['name' => 'required'],
    ],
    'name max' => [
        CreatePipelineRequestFactory::new()->state(['name' => str()->random(256)]),
        ['name' => 'max'],
    ],
    'description required' => [
        CreatePipelineRequestFactory::new()->without('description'),
        ['description' => 'required'],
    ],
    'description max' => [
        CreatePipelineRequestFactory::new()->state(['description' => str()->random(65536)]),
        ['description' => 'max'],
    ],
]);

it('can create pipelines', function () {
    $undoRepeaterFake = Repeater::fake();

    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $pipelineData = CreatePipelineRequestFactory::new()->create();

    livewire(CreatePipeline::class)
        ->fillForm($pipelineData)
        ->call('create')
        ->assertHasNoFormErrors();

    $createdPipeline = Pipeline::first();
    assertCount(1, Pipeline::all());
    expect($createdPipeline->stages)->toHaveCount(3);

    $undoRepeaterFake();
});
