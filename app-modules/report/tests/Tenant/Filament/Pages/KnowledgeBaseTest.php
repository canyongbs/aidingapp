<?php

use AidingApp\Report\Filament\Pages\KnowledgeBase;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->data->addons->knowledgeManagement = false;
    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    livewire(KnowledgeBase::class)->assertForbidden();

    $user->givePermissionTo('report-library.view-any');
    $user->refresh();

    livewire(KnowledgeBase::class)->assertForbidden();

    $settings->data->addons->serviceManagement = true;
    $settings->save();

    livewire(KnowledgeBase::class)->assertForbidden();

    $settings->data->addons->knowledgeManagement = true;
    $settings->save();

    livewire(KnowledgeBase::class)->assertOk();
});
