<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Filament\Resources\OrganizationResource;

test('View Organization is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    $organization = Organization::factory()->create();

    actingAs($user)
        ->get(
            OrganizationResource::getUrl('view', [
                'record' => $organization,
            ])
        )->assertForbidden();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.view');

    actingAs($user)
        ->get(
            OrganizationResource::getUrl('view', [
                'record' => $organization,
            ])
        )->assertSuccessful();
});

