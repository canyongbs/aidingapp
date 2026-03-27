<?php

use AidingApp\Report\Filament\Pages\ContractManagement;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->contractManagement = false;
    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    livewire(ContractManagement::class)->assertForbidden();

    $user->givePermissionTo('report-library.view-any');
    $user->refresh();

    livewire(ContractManagement::class)->assertForbidden();

    $settings->data->addons->contractManagement = true;
    $settings->save();

    livewire(ContractManagement::class)->assertOk();
});
