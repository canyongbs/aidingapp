<?php

use AidingApp\Authorization\Models\Permission;
use AidingApp\Authorization\Models\PermissionGroup;
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\InventoryManagement\Models\MaintenanceActivity;
use App\Features\AssetPermissionConsolidationFeature;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

use function Pest\Laravel\actingAs;

use Spatie\Permission\PermissionRegistrar;

beforeEach(function (): void {
    // The feature gate checked by the parent asset policy requires an authenticated user
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

$supportedAbilities = [
    'viewAny' => ['viewAny', 'view-any', false],
    'view' => ['view', '*.view', true],
    'create' => ['create', 'create', false],
    'update' => ['update', '*.update', true],
];

$updateGovernedAbilities = [
    'delete' => ['delete', '*.delete', true],
    'deleteAny' => ['deleteAny', '*.delete', false],
    'restore' => ['restore', '*.restore', true],
    'restoreAny' => ['restoreAny', '*.restore', false],
    'forceDelete' => ['forceDelete', '*.force-delete', true],
    'forceDeleteAny' => ['forceDeleteAny', '*.force-delete', false],
];

it('is governed by the assets permissions', function (string $ability, string $action, bool $forRecord): void {
    $arguments = $forRecord ? MaintenanceActivity::factory()->make() : MaintenanceActivity::class;

    expect(Gate::forUser(User::factory()->create())->inspect($ability, $arguments)->denied())->toBeTrue()
        ->and(Gate::forUser(User::factory()->create()->givePermissionTo("assets.{$action}"))->inspect($ability, $arguments)->allowed())->toBeTrue();
})->with($supportedAbilities);

it('only allows the action for a user who can update the parent asset', function (string $ability, string $action, bool $forRecord) use ($allAssetsPermissions): void {
    $arguments = $forRecord ? MaintenanceActivity::factory()->for(Asset::factory()->create())->make() : MaintenanceActivity::class;

    $withoutUpdate = User::factory()->create()->givePermissionTo(array_diff($allAssetsPermissions, ['assets.*.update']));

    expect(Gate::forUser($withoutUpdate)->inspect($ability, $arguments)->denied())->toBeTrue()
        ->and(Gate::forUser(User::factory()->create()->givePermissionTo('assets.*.update'))->inspect($ability, $arguments)->allowed())->toBeTrue();
})->with($updateGovernedAbilities);

// TODO: Cleanup Task (assets-permission-consolidation): delete this test, the feature will always be active
it('is governed by the maintenance activity permissions while the assets permission consolidation is inactive', function (string $ability, string $action, bool $forRecord) use ($allAssetsPermissions): void {
    AssetPermissionConsolidationFeature::deactivate();

    $legacyPermission = Permission::factory()->create([
        'name' => "maintenance_activity.{$action}",
        'guard_name' => 'web',
        'group_id' => PermissionGroup::create(['name' => 'Maintenance Activity'])->getKey(),
    ]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $arguments = $forRecord ? MaintenanceActivity::factory()->make() : MaintenanceActivity::class;

    expect(Gate::forUser(User::factory()->create()->givePermissionTo($allAssetsPermissions))->inspect($ability, $arguments)->denied())->toBeTrue()
        ->and(Gate::forUser(User::factory()->create()->givePermissionTo($legacyPermission))->inspect($ability, $arguments)->allowed())->toBeTrue();
})->with([...$supportedAbilities, ...$updateGovernedAbilities]);
