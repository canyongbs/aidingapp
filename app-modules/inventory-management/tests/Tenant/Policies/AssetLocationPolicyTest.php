<?php

use AidingApp\Authorization\Models\Permission;
use AidingApp\Authorization\Models\PermissionGroup;
use AidingApp\InventoryManagement\Models\AssetLocation;
use App\Features\AssetPermissionConsolidationFeature;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

use function Pest\Laravel\actingAs;

use Spatie\Permission\PermissionRegistrar;

beforeEach(function (): void {
    // The feature gate checked by the policy requires an authenticated user
    actingAs(User::factory()->create());
});

$abilities = [
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

it('is governed by the assets permissions', function (string $ability, string $action, bool $forRecord): void {
    $arguments = $forRecord ? AssetLocation::factory()->make() : AssetLocation::class;

    expect(Gate::forUser(User::factory()->create())->inspect($ability, $arguments)->denied())->toBeTrue()
        ->and(Gate::forUser(User::factory()->create()->givePermissionTo("assets.{$action}"))->inspect($ability, $arguments)->allowed())->toBeTrue();
})->with($abilities);

// TODO: Cleanup Task (assets-permission-consolidation): delete this test, the feature will always be active
it('is governed by the asset location permissions while the assets permission consolidation is inactive', function (string $ability, string $action, bool $forRecord): void {
    AssetPermissionConsolidationFeature::deactivate();

    $legacyPermission = Permission::factory()->create([
        'name' => "asset_location.{$action}",
        'guard_name' => 'web',
        'group_id' => PermissionGroup::create(['name' => 'Asset Location'])->getKey(),
    ]);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $arguments = $forRecord ? AssetLocation::factory()->make() : AssetLocation::class;

    expect(Gate::forUser(User::factory()->create()->givePermissionTo("assets.{$action}"))->inspect($ability, $arguments)->denied())->toBeTrue()
        ->and(Gate::forUser(User::factory()->create()->givePermissionTo($legacyPermission))->inspect($ability, $arguments)->allowed())->toBeTrue();
})->with($abilities);
