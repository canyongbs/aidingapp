<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource;

test('View OrganizationIndustry is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    $organization_industry = OrganizationIndustry::factory()->create();

    actingAs($user)
        ->get(
            OrganizationIndustryResource::getUrl('view', [
                'record' => $organization_industry,
            ])
        )->assertForbidden();

    $user->givePermissionTo('organization_industry.view-any');
    $user->givePermissionTo('organization_industry.*.view');

    actingAs($user)
        ->get(
            OrganizationIndustryResource::getUrl('view', [
                'record' => $organization_industry,
            ])
        )->assertSuccessful();
});
