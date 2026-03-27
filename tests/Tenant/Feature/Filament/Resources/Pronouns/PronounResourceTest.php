<?php

use App\Filament\Resources\Pronouns\PronounsResource;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(PronounsResource::getUrl('index'))->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->refresh();

    get(PronounsResource::getUrl('index'))->assertOk();
});
