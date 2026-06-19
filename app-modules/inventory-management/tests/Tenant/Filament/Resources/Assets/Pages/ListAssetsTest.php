<?php

use AidingApp\InventoryManagement\Filament\Resources\Assets\Pages\ListAssets;
use AidingApp\InventoryManagement\Filament\Resources\AssetTypes\Pages\ListAssetTypes;
use AidingApp\InventoryManagement\Models\Asset;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('only shows the bulk delete action to a user with the asset.delete permission', function () {
    Asset::factory(15)->create();

    $user = User::factory()
        ->create(['timezone' => config('app.timezone')])
        ->givePermissionTo('asset.view-any', 'asset.*.view');

    actingAs($user);

    livewire(ListAssets::class)
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('asset.*.delete');

    livewire(ListAssets::class)
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});