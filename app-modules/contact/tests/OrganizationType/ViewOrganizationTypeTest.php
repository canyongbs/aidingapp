<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\OrganizationType;
use AidingApp\Contact\Filament\Resources\OrganizationTypeResource;

test('View OrganizationType is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    $organization_type = OrganizationType::factory()->create();

    actingAs($user)
        ->get(
            OrganizationTypeResource::getUrl('view', [
                'record' => $organization_type,
            ])
        )->assertForbidden();

    $user->givePermissionTo('organization_type.view-any');
    $user->givePermissionTo('organization_type.*.view');

    actingAs($user)
        ->get(
            OrganizationTypeResource::getUrl('view', [
                'record' => $organization_type,
            ])
        )->assertSuccessful();
});
