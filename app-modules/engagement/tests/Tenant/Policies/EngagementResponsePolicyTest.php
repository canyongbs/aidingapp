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
use AidingApp\Engagement\Models\EngagementResponse;
use App\Features\ContactPermissionConsolidationFeature;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\PermissionRegistrar;

$abilities = [
    'viewAny' => ['viewAny', 'view-any', false],
    'view' => ['view', '*.view', true],
    'create' => ['create', 'create', false],
    'update' => ['update', '*.update', true],
    'delete' => ['delete', '*.delete', true],
    'restore' => ['restore', '*.restore', true],
    'forceDelete' => ['forceDelete', '*.force-delete', true],
];

it('is governed by the contacts permissions', function (string $ability, string $action, bool $forRecord): void {
    $arguments = $forRecord ? EngagementResponse::factory()->make() : EngagementResponse::class;

    expect(Gate::forUser(User::factory()->create())->inspect($ability, $arguments)->denied())->toBeTrue()
        ->and(Gate::forUser(User::factory()->create()->givePermissionTo("contacts.{$action}"))->inspect($ability, $arguments)->allowed())->toBeTrue();
})->with($abilities);

// TODO: Cleanup Task (contacts-permission-consolidation): delete this test, the feature will always be active
it('is governed by the engagement response permissions while the contacts permission consolidation is inactive', function (string $ability, string $action, bool $forRecord): void {
    ContactPermissionConsolidationFeature::deactivate();

    $legacyPermission = Permission::factory()->create([
        'name' => "engagement_response.{$action}",
        'guard_name' => 'web',
        'group_id' => PermissionGroup::create(['name' => 'Engagement Response'])->getKey(),
    ]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $arguments = $forRecord ? EngagementResponse::factory()->make() : EngagementResponse::class;

    expect(Gate::forUser(User::factory()->create()->givePermissionTo("contacts.{$action}"))->inspect($ability, $arguments)->denied())->toBeTrue()
        ->and(Gate::forUser(User::factory()->create()->givePermissionTo($legacyPermission))->inspect($ability, $arguments)->allowed())->toBeTrue();
})->with($abilities);
