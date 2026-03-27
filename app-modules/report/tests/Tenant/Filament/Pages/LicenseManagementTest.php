<?php

use AidingApp\Report\Filament\Pages\LicenseManagement;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->licenseManagement = false;
    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    livewire(LicenseManagement::class)->assertForbidden();

    $user->givePermissionTo('report-library.view-any');
    $user->refresh();

    livewire(LicenseManagement::class)->assertForbidden();

    $settings->data->addons->licenseManagement = true;
    $settings->save();

    livewire(LicenseManagement::class)->assertOk();
});
