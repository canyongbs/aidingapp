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

use AidingApp\Project\Filament\Resources\Projects\Pages\ManagePipelines;
use AidingApp\Project\Filament\Resources\Projects\RelationManagers\PipelinesRelationManager;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\Project;
use App\Features\PipelineArchivingFeature;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    asSuperAdmin(User::factory()->create());
});

// The archiving flag is activated globally by the archiving migration (RefreshDatabase
// runs it), matching how ProjectWorkPipelineWidgetTest treats the active default and
// only ever calls deactivate() for the inactive cases. We still activate explicitly in
// the active-path tests for clarity.

it('hides archived pipelines when the archiving flag is active', function () {
    PipelineArchivingFeature::activate();

    $project = Project::factory()->create();
    $active = Pipeline::factory()->for($project)->create(['created_by_id' => auth()->id()]);
    $archived = Pipeline::factory()->for($project)->create(['created_by_id' => auth()->id()]);
    $archived->archive();

    livewire(PipelinesRelationManager::class, [
        'ownerRecord' => $project,
        'pageClass' => ManagePipelines::class,
    ])
        ->assertCanSeeTableRecords([$active])
        ->assertCanNotSeeTableRecords([$archived]);
});

it('shows archived pipelines when the archiving flag is inactive', function () {
    PipelineArchivingFeature::deactivate();
    $project = Project::factory()->create();
    $active = Pipeline::factory()->for($project)->create(['created_by_id' => auth()->id()]);
    $archived = Pipeline::factory()->for($project)->create(['created_by_id' => auth()->id()]);
    $archived->archive();

    livewire(PipelinesRelationManager::class, [
        'ownerRecord' => $project,
        'pageClass' => ManagePipelines::class,
    ])
        ->assertCanSeeTableRecords([$active, $archived]);
});

it('archives a pipeline through the row action', function () {
    PipelineArchivingFeature::activate();

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()->for($project)->create(['created_by_id' => auth()->id()]);

    livewire(PipelinesRelationManager::class, [
        'ownerRecord' => $project,
        'pageClass' => ManagePipelines::class,
    ])
        ->callAction(TestAction::make('archive')->table($pipeline))
        ->assertHasNoActionErrors();

    expect($pipeline->refresh()->isArchived())->toBeTrue();
});
