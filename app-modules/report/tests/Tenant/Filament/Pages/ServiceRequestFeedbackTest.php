<?php

use AidingApp\Report\Filament\Pages\ServiceRequestFeedback;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->data->addons->feedbackManagement = false;
    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    livewire(ServiceRequestFeedback::class)->assertForbidden();

    $user->givePermissionTo('report-library.view-any');
    $user->refresh();

    livewire(ServiceRequestFeedback::class)->assertForbidden();

    $settings->data->addons->serviceManagement = true;
    $settings->save();

    livewire(ServiceRequestFeedback::class)->assertForbidden();

    $settings->data->addons->feedbackManagement = true;
    $settings->save();

    livewire(ServiceRequestFeedback::class)->assertOk();
});
