<?php

use AidingApp\Contact\Filament\Resources\OrganizationResource\Pages\ListOrganizations;
use AidingApp\Contact\Models\Organization;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('only shows the bulk delete action to a user with the organization.delete permission', function () {
    Organization::factory(15)->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('organization.view-any', 'organization.*.view');

    actingAs($user);

    livewire(ListOrganizations::class)
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('organization.*.delete');

    livewire(ListOrganizations::class)
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});