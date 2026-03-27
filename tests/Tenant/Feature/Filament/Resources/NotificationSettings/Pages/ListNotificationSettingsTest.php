<?php

use App\Filament\Resources\NotificationSettings\Pages\ListNotificationSettings;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    livewire(ListNotificationSettings::class)->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->refresh();

    livewire(ListNotificationSettings::class)->assertOk();
});
