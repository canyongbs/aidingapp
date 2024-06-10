<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use AidingApp\Contact\Filament\Resources\OrganizationResource;
use AidingApp\Contact\Models\Contact;

test('ListOrganization is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    actingAs($user)
        ->get(
            OrganizationResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('organization.view-any');

    actingAs($user)
        ->get(
            OrganizationResource::getUrl('index')
        )->assertSuccessful();
});
