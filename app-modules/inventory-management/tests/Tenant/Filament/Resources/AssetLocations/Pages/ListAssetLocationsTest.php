<?php

use AidingApp\InventoryManagement\Filament\Resources\AssetLocations\Pages\ListAssetLocations;
use AidingApp\InventoryManagement\Filament\Resources\Assets\Pages\ListAssets;
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\InventoryManagement\Models\AssetLocation;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('only shows the bulk delete action to a user with the asset_location.delete permission', function () {
    AssetLocation::factory(15)->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('asset_location.view-any', 'asset_location.*.view');

    actingAs($user);

    livewire(ListAssetLocations::class)
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('asset_location.*.delete');

    livewire(ListAssetLocations::class)
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});