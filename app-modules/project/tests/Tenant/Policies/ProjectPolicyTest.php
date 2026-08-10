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

use AidingApp\Project\Models\Project;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

it('allows a super admin to archive any project', function () {
    $user = User::factory()->create();

    asSuperAdmin($user);

    $project = Project::factory()->create();

    expect($user->can('archive', $project))->toBeTrue();
});

it('allows a project manager with the delete permission to archive the project', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.*.delete');

    actingAs($user);

    $project = Project::factory()
        ->for(User::factory(), 'createdBy')
        ->hasAttached($user, relationship: 'managerUsers')
        ->create();

    expect($user->can('archive', $project))->toBeTrue();
});

it('allows the project creator with the delete permission to archive the project', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.*.delete');

    actingAs($user);

    $project = Project::factory()->for($user, 'createdBy')->create();

    expect($user->can('archive', $project))->toBeTrue();
});

it('does not let a manager archive the project without the delete permission', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()
        ->for(User::factory(), 'createdBy')
        ->hasAttached($user, relationship: 'managerUsers')
        ->create();

    expect($user->can('archive', $project))->toBeFalse();
});

it('does not fail when the project has no creator', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.*.delete');

    $project = Project::factory()->create();

    actingAs($user);

    expect($project->createdBy)->toBeNull()
        ->and($user->can('archive', $project))->toBeFalse();
});

it('does not let a user with the delete permission archive a project they do not manage or create', function () {
    $creator = User::factory()->create();

    actingAs($creator);

    $project = Project::factory()->create();

    $user = User::factory()->create();

    $user->givePermissionTo('project.*.delete');

    actingAs($user);

    expect($user->can('archive', $project))->toBeFalse();
});
