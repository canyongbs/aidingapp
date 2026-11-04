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

use AidingApp\Project\Filament\Resources\Pipelines\Pages\EditPipeline;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Tests\Tenant\Filament\Resources\PipelineResource\RequestFactory\EditPipelineRequestFactory;
use App\Features\PipelineNameUniquenessFeature;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Tests\asSuperAdmin;

it('can render with proper permission.', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->has(PipelineStage::factory()->count(3), 'stages')
        ->for($project)
        ->state([
            'name' => 'Test Pipeline',
            'description' => 'Test pipeline description',
        ])->create();

    Livewire::test(EditPipeline::class, [
        'record' => $pipeline->getKey(),
        'parentRecord' => $project,
    ])
        ->assertForbidden();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('pipeline.view-any');
    $user->givePermissionTo('project.*.update');
    $user->givePermissionTo('pipeline.*.update');
    $user->refresh();

    Livewire::test(EditPipeline::class, [
        'record' => $pipeline->getKey(),
        'parentRecord' => $project,
    ])
        ->assertSuccessful();
});

it('can validate edit pipeline inputs', function (EditPipelineRequestFactory $data, array $errors): void {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()
        ->has(PipelineStage::factory()->count(2), 'stages')
        ->for($project)
        ->create();

    $request = EditPipelineRequestFactory::new($data)->create();

    Livewire::test(EditPipeline::class, [
        'record' => $pipeline->getKey(),
        'parentRecord' => $project,
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [
        EditPipelineRequestFactory::new()->state(['name' => null]),
        ['name' => 'required'],
    ],
    'name max' => [
        EditPipelineRequestFactory::new()->state(['name' => str()->random(256)]),
        ['name' => 'max'],
    ],
    'description required' => [
        EditPipelineRequestFactory::new()->state(['description' => null]),
        ['description' => 'required'],
    ],
    'description max' => [
        EditPipelineRequestFactory::new()->state(['description' => str()->random(65536)]),
        ['description' => 'max'],
    ],
]);

it('can edit pipelines', function (): void {
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

    Livewire::test(EditPipeline::class, [
        'record' => $pipeline->getKey(),
        'parentRecord' => $project,
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

it('can delete pipelines', function (): void {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $project = Project::factory()->create();

    $pipeline = Pipeline::factory()
        ->has(PipelineStage::factory()->count(3), 'stages')
        ->for($project)
        ->create();

    Livewire::test(EditPipeline::class, [
        'record' => $pipeline->getKey(),
        'parentRecord' => $project,
    ])
        ->callAction('delete')
        ->assertRedirect();
});

// TODO: Cleanup Task (pipeline-name-uniqueness): keep these tests, but remove the feature flag describe block below
describe('name uniqueness', function (): void {
    it('rejects a pipeline name that differs only by case from another pipeline', function (string $name): void {
        asSuperAdmin();

        $project = Project::factory()->create();
        Pipeline::factory()->for($project)->create(['name' => 'Admissions']);
        $pipeline = Pipeline::factory()->for($project)->has(PipelineStage::factory(), 'stages')->create(['name' => 'Retention']);

        Livewire::test(EditPipeline::class, ['record' => $pipeline->getKey(), 'parentRecord' => $project])
            ->fillForm(['name' => $name])
            ->call('save')
            ->assertHasFormErrors(['name' => 'unique']);

        expect($pipeline->refresh()->name)->toBe('Retention');
    })->with([
        'exact' => 'Admissions',
        'lower case' => 'admissions',
        'upper case' => 'ADMISSIONS',
    ]);

    it('allows a pipeline name that is already used by a pipeline in another project', function (): void {
        asSuperAdmin();

        Pipeline::factory()->create(['name' => 'Admissions']);
        $project = Project::factory()->create();
        $pipeline = Pipeline::factory()->for($project)->has(PipelineStage::factory(), 'stages')->create(['name' => 'Retention']);

        Livewire::test(EditPipeline::class, ['record' => $pipeline->getKey(), 'parentRecord' => $project])
            ->fillForm(['name' => 'admissions'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($pipeline->refresh()->name)->toBe('admissions');
    });

    it('allows a pipeline to keep or change the case of its own name', function (string $name): void {
        asSuperAdmin();

        $project = Project::factory()->create();
        $pipeline = Pipeline::factory()->for($project)->has(PipelineStage::factory(), 'stages')->create(['name' => 'Admissions']);

        Livewire::test(EditPipeline::class, ['record' => $pipeline->getKey(), 'parentRecord' => $project])
            ->fillForm(['name' => $name])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($pipeline->refresh()->name)->toBe($name);
    })->with([
        'same' => 'Admissions',
        'different case' => 'ADMISSIONS',
    ]);

    it('rejects stage names within the pipeline that differ only by case', function (): void {
        asSuperAdmin();

        $project = Project::factory()->create();
        $pipeline = Pipeline::factory()->for($project)->create();
        $first = PipelineStage::factory()->for($pipeline)->create(['name' => 'Review', 'order' => 1]);
        $second = PipelineStage::factory()->for($pipeline)->create(['name' => 'Approval', 'order' => 2]);

        Livewire::test(EditPipeline::class, ['record' => $pipeline->getKey(), 'parentRecord' => $project])
            ->set("data.stages.record-{$second->getKey()}.name", 'REVIEW')
            ->call('save')
            ->assertHasFormErrors(["stages.record-{$first->getKey()}.name", "stages.record-{$second->getKey()}.name"]);

        expect($second->refresh()->name)->toBe('Approval');
    });

    it('allows a stage to change the case of its own name', function (): void {
        asSuperAdmin();

        $project = Project::factory()->create();
        $pipeline = Pipeline::factory()->for($project)->create();
        $stage = PipelineStage::factory()->for($pipeline)->create(['name' => 'Review', 'order' => 1]);

        Livewire::test(EditPipeline::class, ['record' => $pipeline->getKey(), 'parentRecord' => $project])
            ->set("data.stages.record-{$stage->getKey()}.name", 'REVIEW')
            ->call('save')
            ->assertHasNoFormErrors();

        expect($stage->refresh()->name)->toBe('REVIEW');
    });

    it('allows renaming several stages at once even when an earlier stage takes the name of a later one', function (): void {
        asSuperAdmin();

        $project = Project::factory()->create();
        $pipeline = Pipeline::factory()->for($project)->create();
        $planning = PipelineStage::factory()->for($pipeline)->create(['name' => 'Planning', 'order' => 1]);
        $review = PipelineStage::factory()->for($pipeline)->create(['name' => 'Review', 'order' => 2]);

        Livewire::test(EditPipeline::class, ['record' => $pipeline->getKey(), 'parentRecord' => $project])
            ->set("data.stages.record-{$planning->getKey()}.name", 'REVIEW')
            ->set("data.stages.record-{$review->getKey()}.name", 'Done')
            ->call('save')
            ->assertHasNoFormErrors();

        expect($planning->refresh()->name)->toBe('REVIEW')
            ->and($review->refresh()->name)->toBe('Done');
    });

    it('allows swapping the names of two stages', function (): void {
        asSuperAdmin();

        $project = Project::factory()->create();
        $pipeline = Pipeline::factory()->for($project)->create();
        $first = PipelineStage::factory()->for($pipeline)->create(['name' => 'Review', 'order' => 1]);
        $second = PipelineStage::factory()->for($pipeline)->create(['name' => 'Approval', 'order' => 2]);

        Livewire::test(EditPipeline::class, ['record' => $pipeline->getKey(), 'parentRecord' => $project])
            ->set("data.stages.record-{$first->getKey()}.name", 'approval')
            ->set("data.stages.record-{$second->getKey()}.name", 'review')
            ->call('save')
            ->assertHasNoFormErrors();

        expect($first->refresh()->name)->toBe('approval')
            ->and($second->refresh()->name)->toBe('review');
    });

    describe('feature flag', function (): void {
        it('does not apply the unique form rule when the feature is disabled', function (): void {
            PipelineNameUniquenessFeature::deactivate();

            asSuperAdmin();

            $project = Project::factory()->create();
            Pipeline::factory()->for($project)->create(['name' => 'Admissions']);
            $pipeline = Pipeline::factory()->for($project)->has(PipelineStage::factory(), 'stages')->create(['name' => 'Retention']);

            expect(fn () => Livewire::test(EditPipeline::class, ['record' => $pipeline->getKey(), 'parentRecord' => $project])
                ->fillForm(['name' => 'Admissions'])
                ->call('save'))
                ->toThrow(UniqueConstraintViolationException::class);
        });
    });
});
