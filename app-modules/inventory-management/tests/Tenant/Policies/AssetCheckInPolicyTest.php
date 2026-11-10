<?php

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
