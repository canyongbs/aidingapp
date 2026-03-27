<?php

use App\Filament\Resources\Tags\Pages\ListTags;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->knowledgeManagement = false;
    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    livewire(ListTags::class)->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->refresh();

    livewire(ListTags::class)->assertForbidden();

    $settings->data->addons->knowledgeManagement = true;
    $settings->save();

    livewire(ListTags::class)->assertOk();
});
