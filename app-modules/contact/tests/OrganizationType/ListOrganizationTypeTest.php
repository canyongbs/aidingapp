<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Filament\Resources\OrganizationTypeResource;

test('List OrganizationType is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    actingAs($user)
        ->get(
            OrganizationTypeResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('organization_type.view-any');

    actingAs($user)
        ->get(
            OrganizationTypeResource::getUrl('index')
        )->assertSuccessful();
});
