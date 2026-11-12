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
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\InventoryManagement\Models\AssetCheckIn;
use App\Features\AssetPermissionConsolidationFeature;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

use function Pest\Laravel\actingAs;

use Spatie\Permission\PermissionRegistrar;

beforeEach(function (): void {
    // The feature gate checked by the policy requires an authenticated user
    actingAs(User::factory()->create());
});

$allAssetsPermissions = [
    'assets.view-any',
    'assets.create',
    'assets.*.view',
    'assets.*.update',
    'assets.*.delete',
    'assets.*.restore',
    'assets.*.force-delete',
];

$viewAbilities = [
    'viewAny' => ['viewAny', 'view-any', false],
    'view' => ['view', '*.view', true],
];

$updateGovernedAbilities = [
    'update' => ['update', '*.update', true],
    'delete' => ['delete', '*.delete', true],
    'deleteAny' => ['deleteAny', '*.delete', false],
    'restore' => ['restore', '*.restore', true],
    'restoreAny' => ['restoreAny', '*.restore', false],
    'forceDelete' => ['forceDelete', '*.force-delete', true],
    'forceDeleteAny' => ['forceDeleteAny', '*.force-delete', false],
];

it('is governed by the assets permissions for viewing', function (string $ability, string $action, bool $forRecord): void {
    $arguments = $forRecord ? AssetCheckIn::factory()->make() : AssetCheckIn::class;

    expect(Gate::forUser(User::factory()->create())->inspect($ability, $arguments)->denied())->toBeTrue()
        ->and(Gate::forUser(User::factory()->create()->givePermissionTo("assets.{$action}"))->inspect($ability, $arguments)->allowed())->toBeTrue();
})->with($viewAbilities);

it('only allows creating for an asset the user can update', function () use ($allAssetsPermissions): void {
    $asset = Asset::factory()->create();

    $withoutUpdate = User::factory()->create()->givePermissionTo(array_diff($allAssetsPermissions, ['assets.*.update']));

    expect(Gate::forUser(User::factory()->create())->inspect('create', [AssetCheckIn::class, $asset])->denied())->toBeTrue()
        ->and(Gate::forUser($withoutUpdate)->inspect('create', [AssetCheckIn::class, $asset])->denied())->toBeTrue()
        ->and(Gate::forUser(User::factory()->create()->givePermissionTo('assets.*.update'))->inspect('create', [AssetCheckIn::class, $asset])->allowed())->toBeTrue();
});

it('only allows the action for a user who can update the parent asset', function (string $ability, string $action, bool $forRecord) use ($allAssetsPermissions): void {
    $arguments = $forRecord ? AssetCheckIn::factory()->for(Asset::factory()->create())->make() : AssetCheckIn::class;

    $withoutUpdate = User::factory()->create()->givePermissionTo(array_diff($allAssetsPermissions, ['assets.*.update']));

    expect(Gate::forUser($withoutUpdate)->inspect($ability, $arguments)->denied())->toBeTrue()
        ->and(Gate::forUser(User::factory()->create()->givePermissionTo('assets.*.update'))->inspect($ability, $arguments)->allowed())->toBeTrue();
})->with($updateGovernedAbilities);

// TODO: Cleanup Task (assets-permission-consolidation): delete this test, the feature will always be active
it('is governed by the asset check in permissions while the assets permission consolidation is inactive', function (string $ability, string $action, bool $forRecord) use ($allAssetsPermissions): void {
    AssetPermissionConsolidationFeature::deactivate();

    $legacyPermission = Permission::factory()->create([
        'name' => "asset_check_in.{$action}",
        'guard_name' => 'web',
        'group_id' => PermissionGroup::create(['name' => 'Asset Check In'])->getKey(),
    ]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $arguments = $forRecord ? AssetCheckIn::factory()->make() : AssetCheckIn::class;

    expect(Gate::forUser(User::factory()->create()->givePermissionTo($allAssetsPermissions))->inspect($ability, $arguments)->denied())->toBeTrue()
        ->and(Gate::forUser(User::factory()->create()->givePermissionTo($legacyPermission))->inspect($ability, $arguments)->allowed())->toBeTrue();
})->with([...$viewAbilities, ...$updateGovernedAbilities]);
