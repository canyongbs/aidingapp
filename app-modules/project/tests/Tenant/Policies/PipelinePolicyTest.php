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

use AidingApp\Authorization\Models\Permission;
use AidingApp\Authorization\Models\PermissionGroup;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use App\Features\ProjectPermissionConsolidationFeature;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

use function Pest\Laravel\actingAs;

use Spatie\Permission\PermissionRegistrar;

use function Tests\asSuperAdmin;

$standalonePipelineAbilities = [
    'viewAny' => ['viewAny', 'view-any', false],
    'view' => ['view', '*.view', true],
    'create' => ['create', 'create', false],
    'update' => ['update', '*.update', true],
    'delete' => ['delete', '*.delete', true],
    'deleteAny' => ['deleteAny', '*.delete', false],
    'restore' => ['restore', '*.restore', true],
    'restoreAny' => ['restoreAny', '*.restore', false],
    'forceDelete' => ['forceDelete', '*.force-delete', true],
    'forceDeleteAny' => ['forceDeleteAny', '*.force-delete', false],
];

it('governs pipelines without a project with the projects permissions', function (string $ability, string $action, bool $forRecord): void {
    $arguments = $forRecord ? Pipeline::factory()->make(['project_id' => null]) : Pipeline::class;

    $user = User::factory()->create();

    actingAs($user);

    expect($user->can($ability, $arguments))->toBeFalse();

    $user->givePermissionTo("projects.{$action}");

    expect($user->can($ability, $arguments))->toBeTrue();
})->with($standalonePipelineAbilities);

// TODO: Cleanup Task (projects-permission-consolidation): delete this test, the feature will always be active
it('governs pipelines without a project with the pipeline permissions while the projects permission consolidation is inactive', function (string $ability, string $action, bool $forRecord): void {
    ProjectPermissionConsolidationFeature::deactivate();

    $legacyPermission = Permission::factory()->create([
        'name' => "pipeline.{$action}",
        'guard_name' => 'web',
        'group_id' => PermissionGroup::create(['name' => 'Pipeline'])->getKey(),
    ]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $arguments = $forRecord ? Pipeline::factory()->make(['project_id' => null]) : Pipeline::class;

    $user = User::factory()->create()->givePermissionTo("projects.{$action}");

    actingAs($user);

    expect($user->can($ability, $arguments))->toBeFalse();

    $legacyUser = User::factory()->create()->givePermissionTo($legacyPermission);

    actingAs($legacyUser);

    expect($legacyUser->can($ability, $arguments))->toBeTrue();
})->with($standalonePipelineAbilities);

it('delegates viewAny to the project when a project is passed', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->for($user, 'createdBy')->create();

    expect($user->can('viewAny', [Pipeline::class, $project]))->toBeFalse();

    $user->givePermissionTo('projects.view-any');
    $user->givePermissionTo('projects.*.view');
    $user->refresh();

    expect($user->can('viewAny', [Pipeline::class, $project]))->toBeTrue();
});

it('falls back to the `projects.view-any` permission when no project is passed', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    expect($user->can('viewAny', Pipeline::class))->toBeFalse();

    $user->givePermissionTo('projects.view-any');
    $user->refresh();

    expect($user->can('viewAny', Pipeline::class))->toBeTrue();
});

it('delegates create to the project when a project is passed', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->for($user, 'createdBy')->create();

    expect($user->can('create', [Pipeline::class, $project]))->toBeFalse();

    $user->givePermissionTo('projects.view-any');
    $user->givePermissionTo('projects.*.view');
    $user->givePermissionTo('projects.*.update');
    $user->refresh();

    expect($user->can('create', [Pipeline::class, $project]))->toBeTrue();
});

it('falls back to the `projects.create` permission when no project is passed', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    expect($user->can('create', Pipeline::class))->toBeFalse();

    $user->givePermissionTo('projects.create');
    $user->refresh();

    expect($user->can('create', Pipeline::class))->toBeTrue();
});

it('delegates update to the pipeline\'s project when it has one', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->for($user, 'createdBy')->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    expect($user->can('update', $pipeline))->toBeFalse();

    $user->givePermissionTo('projects.view-any');
    $user->givePermissionTo('projects.*.view');
    $user->givePermissionTo('projects.*.update');
    $user->refresh();

    expect($user->can('update', $pipeline))->toBeTrue();
});

it('falls back to the `projects.*.update` permission when the pipeline has no project', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $pipeline = Pipeline::factory()
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create(['project_id' => null]);

    expect($user->can('update', $pipeline))->toBeFalse();

    $user->givePermissionTo('projects.*.update');
    $user->refresh();

    expect($user->can('update', $pipeline))->toBeTrue();
});

it('does not let a super admin view or update a pipeline whose project is archived', function (): void {
    $user = User::factory()->create();

    asSuperAdmin($user);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()->for($project)->create();

    expect($user->can('view', $pipeline))->toBeTrue()
        ->and($user->can('update', $pipeline))->toBeTrue();

    $project->archive();

    expect($user->can('view', $pipeline->refresh()))->toBeFalse()
        ->and($user->can('update', $pipeline))->toBeFalse();
});

it('denies access to a pipeline of an archived project without disclosing that it is archived to a user who cannot view the project', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();
    $pipeline = Pipeline::factory()->for($project)->create();

    $project->archive();

    expect(Gate::inspect('view', $pipeline->refresh())->message())
        ->toBe('You do not have permission to view this pipeline\'s project.');
});
