<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource\Pages\EditOrganizationIndustry;
use AidingApp\Contact\Tests\OrganizationIndustry\RequestFactories\EditOrganizationIndustryRequestFactory;

test('Edit OrganizationIndustry is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();
    $organization_industry = OrganizationIndustry::factory()->create();

    actingAs($user)
        ->get(
            OrganizationIndustryResource::getUrl('edit', [
                'record' => $organization_industry,
            ])
        )->assertForbidden();

    livewire(EditOrganizationIndustry::class, [
        'record' => $organization_industry->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('organization_industry.view-any');
    $user->givePermissionTo('organization_industry.*.update');

    actingAs($user)
        ->get(
            OrganizationIndustryResource::getUrl('edit', [
                'record' => $organization_industry,
            ])
        )->assertSuccessful();

    $request = collect(EditOrganizationIndustryRequestFactory::new()->create([
        'created_by_id' => $user->id,
    ]));

    livewire(EditOrganizationIndustry::class, [
        'record' => $organization_industry->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    $organization_industry->refresh();

    expect($organization_industry->name)->toEqual($request->get('name'));
});
