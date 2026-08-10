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

use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('delegates viewAny to the project when a project is passed', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->for($user, 'createdBy')->create();

    expect($user->can('viewAny', [Pipeline::class, $project]))->toBeFalse();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->refresh();

    expect($user->can('viewAny', [Pipeline::class, $project]))->toBeTrue();
});

it('falls back to the pipeline.view-any permission when no project is passed', function () {
    $user = User::factory()->create();

    actingAs($user);

    expect($user->can('viewAny', Pipeline::class))->toBeFalse();

    $user->givePermissionTo('pipeline.view-any');
    $user->refresh();

    expect($user->can('viewAny', Pipeline::class))->toBeTrue();
});

it('delegates create to the project when a project is passed', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->for($user, 'createdBy')->create();

    expect($user->can('create', [Pipeline::class, $project]))->toBeFalse();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('project.*.update');
    $user->refresh();

    expect($user->can('create', [Pipeline::class, $project]))->toBeTrue();
});

it('falls back to the pipeline.create permission when no project is passed', function () {
    $user = User::factory()->create();

    actingAs($user);

    expect($user->can('create', Pipeline::class))->toBeFalse();

    $user->givePermissionTo('pipeline.create');
    $user->refresh();

    expect($user->can('create', Pipeline::class))->toBeTrue();
});

it('delegates update to the pipeline\'s project when it has one', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->for($user, 'createdBy')->create();

    $pipeline = Pipeline::factory()
        ->for($project)
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create();

    expect($user->can('update', $pipeline))->toBeFalse();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('project.*.update');
    $user->refresh();

    expect($user->can('update', $pipeline))->toBeTrue();
});

it('falls back to the pipeline.*.update permission when the pipeline has no project', function () {
    $user = User::factory()->create();

    actingAs($user);

    $pipeline = Pipeline::factory()
        ->has(PipelineStage::factory()->count(1), 'stages')
        ->create(['project_id' => null]);

    expect($user->can('update', $pipeline))->toBeFalse();

    $user->givePermissionTo('pipeline.*.update');
    $user->refresh();

    expect($user->can('update', $pipeline))->toBeTrue();
});
