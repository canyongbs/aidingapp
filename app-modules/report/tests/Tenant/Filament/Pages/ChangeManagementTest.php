<?php

use AidingApp\Report\Filament\Pages\ChangeManagement;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->data->addons->changeManagement = false;
    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    livewire(ChangeManagement::class)->assertForbidden();

    $user->givePermissionTo('report-library.view-any');
    $user->refresh();

    livewire(ChangeManagement::class)->assertForbidden();

    $settings->data->addons->serviceManagement = true;
    $settings->save();

    livewire(ChangeManagement::class)->assertForbidden();

    $settings->data->addons->changeManagement = true;
    $settings->save();

    livewire(ChangeManagement::class)->assertOk();
});
