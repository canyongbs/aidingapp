<?php

use AidingApp\InventoryManagement\Enums\SystemAssetStatusClassification;
use AidingApp\InventoryManagement\Filament\Resources\Assets\Pages\ViewAsset;
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\InventoryManagement\Models\AssetStatus;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

describe('authorization', function (): void {
    it('shows the `check-in` action to a user who can update the asset', function (): void {
        actingAs(User::factory()->create(['timezone' => config('app.timezone')])->givePermissionTo('assets.view-any', 'assets.*.view', 'assets.*.update'));

        AssetStatus::factory()->available()->create();

        $asset = Asset::factory()->create([
            'status_id' => AssetStatus::factory()->create(['classification' => SystemAssetStatusClassification::CheckedOut]),
        ]);

        Livewire::test(ViewAsset::class, ['record' => $asset->getKey()])
            ->assertActionVisible('check-in');
    });

    it('hides the `check-in` action from a user who cannot update the asset', function (): void {
        actingAs(User::factory()->create(['timezone' => config('app.timezone')])->givePermissionTo('assets.view-any', 'assets.*.view', 'assets.create'));

        AssetStatus::factory()->available()->create();

        $asset = Asset::factory()->create([
            'status_id' => AssetStatus::factory()->create(['classification' => SystemAssetStatusClassification::CheckedOut]),
        ]);

        Livewire::test(ViewAsset::class, ['record' => $asset->getKey()])
            ->assertActionHidden('check-in');
    });
});
