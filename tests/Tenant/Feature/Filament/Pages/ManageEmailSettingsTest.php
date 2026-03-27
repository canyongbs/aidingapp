<?php

use App\Filament\Pages\ManageEmailSettings;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    livewire(ManageEmailSettings::class)->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->refresh();

    livewire(ManageEmailSettings::class)->assertOk();
});
