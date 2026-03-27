<?php

use AidingApp\InventoryManagement\Filament\Resources\MaintenanceProviders\Pages\ListMaintenanceProviders;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->assetManagement = false;
    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    livewire(ListMaintenanceProviders::class)->assertForbidden();

    $user->givePermissionTo('maintenance_provider.view-any');
    $user->refresh();

    livewire(ListMaintenanceProviders::class)->assertForbidden();

    $settings->data->addons->assetManagement = true;
    $settings->save();

    livewire(ListMaintenanceProviders::class)->assertOk();
});
