<?php

use AidingApp\Group\Filament\Resources\Groups\GroupResource;
use AidingApp\Group\Filament\Resources\Groups\Pages\ViewGroup;
use AidingApp\Group\Models\Group;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render the view group page', function () {
    asSuperAdmin();
    $group = Group::factory()->create();

    get(GroupResource::getUrl('view', ['record' => $group]))->assertSuccessful();
});

it('displays the group data', function () {
    asSuperAdmin();
    $group = Group::factory()->create();

    livewire(ViewGroup::class, ['record' => $group->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $group->name,
            'description' => $group->description,
        ]);
});

describe('authorization', function () {
    it('denies access without the `group.*.view` permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('group.view-any');
        actingAs($user);
        $group = Group::factory()->create();

        get(GroupResource::getUrl('view', ['record' => $group]))->assertForbidden();
    });
});
