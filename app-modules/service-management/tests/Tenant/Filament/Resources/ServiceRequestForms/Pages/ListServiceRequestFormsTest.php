<?php

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestForms\Pages\ListServiceRequestForms;
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

    livewire(ListServiceRequestForms::class)->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->refresh();

    livewire(ListServiceRequestForms::class)->assertForbidden();

    $settings->data->addons->serviceManagement = true;
    $settings->save();

    livewire(ListServiceRequestForms::class)->assertOk();
});
