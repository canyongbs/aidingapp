<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AidingApp\Contact\Models\Contact;

use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;

use AidingApp\Contact\Models\OrganizationType;
use AidingApp\Contact\Filament\Resources\OrganizationTypeResource;
use AidingApp\Contact\Filament\Resources\OrganizationTypeResource\Pages\CreateOrganizationType;
use AidingApp\Contact\Tests\OrganizationType\RequestFactories\CreateOrganizationTypeRequestFactory;

test('Create Organization Type is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    actingAs($user)
        ->get(
            OrganizationTypeResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateOrganizationType::class)
        ->assertForbidden();

    $user->givePermissionTo('organization_type.view-any');
    $user->givePermissionTo('organization_type.create');

    actingAs($user)
        ->get(
            OrganizationTypeResource::getUrl('create')
        )->assertSuccessful();

    livewire(CreateOrganizationType::class)
        ->assertSuccessful();
});
test('Create New Organization Type', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    $user->givePermissionTo('organization_type.view-any');
    $user->givePermissionTo('organization_type.create');

    actingAs($user)
        ->get(
            OrganizationTypeResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateOrganizationTypeRequestFactory::new()->create([
        'created_by_id' => $user->id,
    ]));

    livewire(CreateOrganizationType::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();
    assertCount(1, OrganizationType::all());
    assertDatabaseHas(OrganizationType::class, $request->toArray());
});
