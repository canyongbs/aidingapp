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
use AidingApp\Project\Models\ProjectFile;
use App\Features\ProjectPermissionConsolidationFeature;
use App\Models\User;

use function Pest\Laravel\actingAs;

use Spatie\Permission\PermissionRegistrar;

it('only allows deleting any project file with the `projects.*.update` permission', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    expect($user->can('deleteAny', ProjectFile::class))->toBeFalse();

    $user->givePermissionTo('projects.*.update');

    expect($user->can('deleteAny', ProjectFile::class))->toBeTrue();
});

// TODO: Cleanup Task (projects-permission-consolidation): delete this test, the feature will always be active
it('only allows deleting any project file with the `project.*.update` permission while the projects permission consolidation is inactive', function (): void {
    ProjectPermissionConsolidationFeature::deactivate();

    $legacyPermission = Permission::factory()->create([
        'name' => 'project.*.update',
        'guard_name' => 'web',
        'group_id' => PermissionGroup::create(['name' => 'Project'])->getKey(),
    ]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $user = User::factory()->create()->givePermissionTo('projects.*.update');

    actingAs($user);

    expect($user->can('deleteAny', ProjectFile::class))->toBeFalse();

    $legacyUser = User::factory()->create()->givePermissionTo($legacyPermission);

    actingAs($legacyUser);

    expect($legacyUser->can('deleteAny', ProjectFile::class))->toBeTrue();
});
