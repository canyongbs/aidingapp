<?php

use AidingApp\Report\Filament\Pages\IncidentManagement;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->data->addons->incidentManagement = false;
    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    livewire(IncidentManagement::class)->assertForbidden();

    $user->givePermissionTo('report-library.view-any');
    $user->refresh();

    livewire(IncidentManagement::class)->assertForbidden();

    $settings->data->addons->serviceManagement = true;
    $settings->save();

    livewire(IncidentManagement::class)->assertForbidden();

    $settings->data->addons->incidentManagement = true;
    $settings->save();

    livewire(IncidentManagement::class)->assertOk();
});
