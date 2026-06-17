<?php

use App\Filament\Resources\SystemUsers\Pages\ListSystemUsers;
use App\Models\SystemUser;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('only shows the bulk delete action to a user with the system_user.delete permission', function () {
    SystemUser::factory(15)->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('system_user.view-any', 'system_user.*.view');

    actingAs($user);

    livewire(ListSystemUsers::class)
        ->assertTableBulkActionHidden('delete');

    $user->givePermissionTo('system_user.*.delete');

    livewire(ListSystemUsers::class)
        ->assertTableBulkActionVisible('delete');
});