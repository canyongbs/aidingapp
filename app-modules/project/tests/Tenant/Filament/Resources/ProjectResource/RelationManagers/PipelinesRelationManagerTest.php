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

use AidingApp\Project\Filament\Resources\Pipelines\Pages\CreatePipeline;
use AidingApp\Project\Filament\Resources\Projects\Pages\ManagePipelines;
use AidingApp\Project\Filament\Resources\Projects\RelationManagers\PipelinesRelationManager;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Tests\Tenant\Filament\Resources\PipelineResource\RequestFactory\CreatePipelineRequestFactory;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Repeater;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

it('can list pipelines', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $project = Project::factory()->create();

    $pipelines = Pipeline::factory()
        ->has(PipelineStage::factory()->count(3), 'stages')
        ->for($project)
        ->count(2)
        ->create();

    livewire(PipelinesRelationManager::class, [
        'ownerRecord' => $project,
        'pageClass' => ManagePipelines::class,
    ])
        ->assertCanSeeTableRecords($pipelines);
});

describe('authorization', function () {
    it('allows a super admin to create a pipeline', function () {
        $undoRepeaterFake = Repeater::fake();

        asSuperAdmin();

        $project = Project::factory()->create();

        livewire(PipelinesRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => ManagePipelines::class,
        ])
            ->assertActionVisible(TestAction::make(CreateAction::class)->table());

        $pipelineData = CreatePipelineRequestFactory::new()->create();

        livewire(CreatePipeline::class, ['parentRecord' => $project])
            ->fillForm($pipelineData)
            ->call('create')
            ->assertHasNoFormErrors();

        assertCount(1, Pipeline::all());
        expect(Pipeline::first()->project_id)->toBe($project->getKey());

        $undoRepeaterFake();
    });

    it('allows the project creator with the update permission to create a pipeline', function () {
        $undoRepeaterFake = Repeater::fake();

        $user = User::factory()->create();

        $user->givePermissionTo('project.view-any');
        $user->givePermissionTo('project.*.view');
        $user->givePermissionTo('project.*.update');

        actingAs($user);

        $project = Project::factory()->for($user, 'createdBy')->create();

        livewire(PipelinesRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => ManagePipelines::class,
        ])
            ->assertActionVisible(TestAction::make(CreateAction::class)->table());

        $pipelineData = CreatePipelineRequestFactory::new()->create();

        livewire(CreatePipeline::class, ['parentRecord' => $project])
            ->fillForm($pipelineData)
            ->call('create')
            ->assertHasNoFormErrors();

        assertCount(1, Pipeline::all());
        expect(Pipeline::first()->project_id)->toBe($project->getKey());

        $undoRepeaterFake();
    });

    it('allows an assigned project manager with the update permission to create a pipeline', function () {
        $undoRepeaterFake = Repeater::fake();

        $user = User::factory()->create();

        $user->givePermissionTo('project.view-any');
        $user->givePermissionTo('project.*.view');
        $user->givePermissionTo('project.*.update');

        $project = Project::factory()->for(User::factory(), 'createdBy')->create();

        $project->managerUsers()->attach($user->getKey());

        actingAs($user);

        livewire(PipelinesRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => ManagePipelines::class,
        ])
            ->assertActionVisible(TestAction::make(CreateAction::class)->table());

        $pipelineData = CreatePipelineRequestFactory::new()->create();

        livewire(CreatePipeline::class, ['parentRecord' => $project])
            ->fillForm($pipelineData)
            ->call('create')
            ->assertHasNoFormErrors();

        assertCount(1, Pipeline::all());
        expect(Pipeline::first()->project_id)->toBe($project->getKey());

        $undoRepeaterFake();
    });

    it('hides the create action when the user has no update permission', function () {
        $user = User::factory()->create();

        $user->givePermissionTo('project.view-any');
        $user->givePermissionTo('project.*.view');

        actingAs($user);

        $project = Project::factory()->for($user, 'createdBy')->create();

        livewire(PipelinesRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => ManagePipelines::class,
        ])
            ->assertActionHidden(TestAction::make(CreateAction::class)->table());
    });

    it('hides the create action when the user has the update permission but is unrelated to the project', function () {
        $user = User::factory()->create();

        $user->givePermissionTo('project.view-any');
        $user->givePermissionTo('project.*.view');
        $user->givePermissionTo('project.*.update');

        $project = Project::factory()->for(User::factory(), 'createdBy')->create();

        actingAs($user);

        livewire(PipelinesRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => ManagePipelines::class,
        ])
            ->assertActionHidden(TestAction::make(CreateAction::class)->table());
    });
});
