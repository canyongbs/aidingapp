<?php

use AidingApp\Report\Filament\Pages\ServiceMonitoring;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    livewire(ServiceMonitoring::class)->assertForbidden();

    $user->givePermissionTo('report-library.view-any');
    $user->refresh();

    livewire(ServiceMonitoring::class)->assertForbidden();

    $settings->data->addons->serviceManagement = true;
    $settings->save();

    livewire(ServiceMonitoring::class)->assertOk();
});
