<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;

use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;

use AidingApp\Contact\Filament\Resources\OrganizationResource;
use AidingApp\Contact\Filament\Resources\OrganizationResource\Pages\CreateOrganization;
use AidingApp\Contact\Tests\Organization\RequestFactories\CreateOrganizationRequestFactory;

test('Create Organization is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    actingAs($user)
        ->get(
            OrganizationResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateOrganization::class)
        ->assertForbidden();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.create');

    actingAs($user)
        ->get(
            OrganizationResource::getUrl('create')
        )->assertSuccessful();
});
test('Create New Organization', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.create');

    $request = collect(CreateOrganizationRequestFactory::new()->create());

    actingAs($user);

    livewire(CreateOrganization::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();
    assertCount(1, Organization::all());
    assertDatabaseHas(Organization::class, $request->toArray());
});
