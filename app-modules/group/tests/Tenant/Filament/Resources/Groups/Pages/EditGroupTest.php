<?php

use AidingApp\Group\Filament\Resources\Groups\GroupResource;
use AidingApp\Group\Filament\Resources\Groups\Pages\EditGroup;
use AidingApp\Group\Models\Group;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render the edit group page', function () {
    asSuperAdmin();
    $group = Group::factory()->create();

    get(GroupResource::getUrl('edit', ['record' => $group]))->assertSuccessful();
});

it('displays the group data', function () {
    asSuperAdmin();
    $group = Group::factory()->create();

    livewire(EditGroup::class, ['record' => $group->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $group->name,
            'description' => $group->description,
        ]);
});

it('can update a group', function () {
    asSuperAdmin();
    $group = Group::factory()->create();

    livewire(EditGroup::class, ['record' => $group->getRouteKey()])
        ->fillForm(['name' => 'Advisors', 'description' => 'Academic advisors'])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Group::class, [
        'id' => $group->getKey(),
        'name' => 'Advisors',
        'description' => 'Academic advisors',
    ]);
});

it('allows saving with the same name as the current group', function () {
    asSuperAdmin();
    $group = Group::factory()->create();

    livewire(EditGroup::class, ['record' => $group->getRouteKey()])
        ->fillForm(['name' => $group->name])
        ->call('save')
        ->assertHasNoFormErrors();
});

describe('authorization', function () {
    it('denies access without the `group.*.update` permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('group.view-any');
        actingAs($user);
        $group = Group::factory()->create();

        get(GroupResource::getUrl('edit', ['record' => $group]))->assertForbidden();
    });
});
