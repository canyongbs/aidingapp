<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AidingApp\Contact\Models\Contact;

use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;

use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource\Pages\CreateOrganizationIndustry;
use AidingApp\Contact\Tests\OrganizationIndustry\RequestFactories\CreateOrganizationIndustryRequestFactory;

test('Create Organization Industry is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    actingAs($user)
        ->get(
            OrganizationIndustryResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateOrganizationIndustry::class)
        ->assertForbidden();

    $user->givePermissionTo('organization_industry.view-any');
    $user->givePermissionTo('organization_industry.create');

    actingAs($user)
        ->get(
            OrganizationIndustryResource::getUrl('create')
        )->assertSuccessful();
});
test('Create New Organization Industry', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    $user->givePermissionTo('organization_industry.view-any');
    $user->givePermissionTo('organization_industry.create');

    actingAs($user);

    $request = collect(CreateOrganizationIndustryRequestFactory::new()->create());

    livewire(CreateOrganizationIndustry::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();
    assertCount(1, OrganizationIndustry::all());
    assertDatabaseHas(OrganizationIndustry::class, $request->toArray());
});
