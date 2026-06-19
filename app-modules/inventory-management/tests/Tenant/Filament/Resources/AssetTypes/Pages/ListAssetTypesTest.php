<?php

use AidingApp\InventoryManagement\Filament\Resources\AssetTypes\Pages\ListAssetTypes;
use AidingApp\InventoryManagement\Models\AssetType;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('only shows the bulk delete action to a user with the asset_type.delete permission', function () {
    AssetType::factory(15)->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('asset_type.view-any', 'asset_type.*.view');

    actingAs($user);

    livewire(ListAssetTypes::class)
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('asset_type.*.delete');

    livewire(ListAssetTypes::class)
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});