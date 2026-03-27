<?php

use AidingApp\Report\Filament\Pages\TaskManagement;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->projectManagement = false;
    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    livewire(TaskManagement::class)->assertForbidden();

    $user->givePermissionTo('report-library.view-any');
    $user->refresh();

    livewire(TaskManagement::class)->assertForbidden();

    $settings->data->addons->projectManagement = true;
    $settings->save();

    livewire(TaskManagement::class)->assertOk();
});
