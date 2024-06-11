<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource;

test('List OrganizationIndustry is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    actingAs($user)
        ->get(
            OrganizationIndustryResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('organization_industry.view-any');

    actingAs($user)
        ->get(
            OrganizationIndustryResource::getUrl('index')
        )->assertSuccessful();
});
