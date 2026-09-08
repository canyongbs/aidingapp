<?php

use AidingApp\Group\Filament\Resources\Groups\GroupResource;
use AidingApp\Group\Filament\Resources\Groups\Pages\ListGroups;
use AidingApp\Group\Models\Group;
use App\Features\GroupManagementFeature;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render the list groups page', function () {
    asSuperAdmin();

    get(GroupResource::getUrl())->assertSuccessful();
});

it('can list groups with their descriptions and member counts', function () {
    asSuperAdmin();

    $group = Group::factory()->create(['description' => 'Peer mentoring cohort']);
    $group->users()->attach(User::factory()->count(2)->create());

    livewire(ListGroups::class)
        ->assertCanSeeTableRecords([$group])
        ->assertTableColumnStateSet('users_count', 2, record: $group)
        ->assertSee('Peer mentoring cohort');
});

it('can search and sort groups by name', function () {
    asSuperAdmin();

    $alpha = Group::factory()->create(['name' => 'Alpha']);
    $beta = Group::factory()->create(['name' => 'Beta']);

    livewire(ListGroups::class)
        ->searchTable('Alpha')
        ->assertCanSeeTableRecords([$alpha])
        ->assertCanNotSeeTableRecords([$beta]);

    livewire(ListGroups::class)
        ->sortTable('name')
        ->assertCanSeeTableRecords([$alpha, $beta], inOrder: true);
});

describe('authorization', function () {
    it('denies access without the `group.view-any` permission', function () {
        actingAs(User::factory()->create());

        get(GroupResource::getUrl())->assertForbidden();
    });

    it('denies access when Group management is inactive', function () {
        asSuperAdmin();
        GroupManagementFeature::deactivate();

        get(GroupResource::getUrl())->assertForbidden();
    });
});
