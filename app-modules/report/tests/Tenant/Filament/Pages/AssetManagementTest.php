<?php

use AidingApp\Report\Filament\Pages\AssetManagement;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->data->addons->assetManagement = false;
    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    livewire(AssetManagement::class)->assertForbidden();

    $user->givePermissionTo('report-library.view-any');
    $user->refresh();

    livewire(AssetManagement::class)->assertForbidden();

    $settings->data->addons->serviceManagement = true;
    $settings->save();

    livewire(AssetManagement::class)->assertForbidden();

    $settings->data->addons->assetManagement = true;
    $settings->save();

    livewire(AssetManagement::class)->assertOk();
});
