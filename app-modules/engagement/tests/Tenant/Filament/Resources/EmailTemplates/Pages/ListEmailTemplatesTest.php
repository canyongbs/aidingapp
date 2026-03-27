<?php

use AidingApp\Engagement\Filament\Resources\EmailTemplates\Pages\ListEmailTemplates;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    livewire(ListEmailTemplates::class)->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->refresh();

    livewire(ListEmailTemplates::class)->assertOk();
});
