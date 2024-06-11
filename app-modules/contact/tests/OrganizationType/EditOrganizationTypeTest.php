<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\OrganizationType;
use AidingApp\Contact\Filament\Resources\OrganizationTypeResource;
use AidingApp\Contact\Filament\Resources\OrganizationTypeResource\Pages\EditOrganizationType;
use AidingApp\Contact\Tests\OrganizationType\RequestFactories\EditOrganizationTypeRequestFactory;

test('Edit OrganizationType is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();
    $organization_type = OrganizationType::factory()->create();

    actingAs($user)
        ->get(
            OrganizationTypeResource::getUrl('edit', [
                'record' => $organization_type,
            ])
        )->assertForbidden();

    livewire(EditOrganizationType::class, [
        'record' => $organization_type->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('organization_type.view-any');
    $user->givePermissionTo('organization_type.*.update');

    actingAs($user)
        ->get(
            OrganizationTypeResource::getUrl('edit', [
                'record' => $organization_type,
            ])
        )->assertSuccessful();

    $request = collect(EditOrganizationTypeRequestFactory::new()->create([
        'created_by_id' => $user->id,
    ]));

    livewire(EditOrganizationType::class, [
        'record' => $organization_type->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    $organization_type->refresh();

    expect($organization_type->name)->toEqual($request->get('name'));
});
