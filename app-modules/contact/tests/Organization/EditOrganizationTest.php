<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Filament\Resources\OrganizationResource;
use AidingApp\Contact\Filament\Resources\OrganizationResource\Pages\EditOrganization;
use AidingApp\Contact\Tests\Organization\RequestFactories\EditOrganizationRequestFactory;

test('Edit Organization is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();
    $organization = Organization::factory()->create();

    actingAs($user)
        ->get(
            OrganizationResource::getUrl('edit', [
                'record' => $organization,
            ])
        )->assertForbidden();

    livewire(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    actingAs($user)
        ->get(
            OrganizationResource::getUrl('edit', [
                'record' => $organization,
            ])
        )->assertSuccessful();

    $request = collect(EditOrganizationRequestFactory::new()->create([
        'created_by_id' => $user->id,
    ]));

    livewire(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    $organization->refresh();

    expect($organization->name)->toEqual($request->get('name'))
        ->and($organization->email)->toEqual($request->get('email'))
        ->and($organization->phone_number)->toEqual($request->get('phone_number'))
        ->and($organization->website)->toEqual($request->get('website'))
        ->and($organization->type_id)->toEqual($request->get('type_id'))
        ->and($organization->industry_id)->toEqual($request->get('industry_id'))
        ->and($organization->number_of_employees)->toEqual($request->get('number_of_employees'))
        ->and($organization->address)->toEqual($request->get('address'))
        ->and($organization->city)->toEqual($request->get('city'))
        ->and($organization->state)->toEqual($request->get('state'))
        ->and($organization->postalcode)->toEqual($request->get('postalcode'))
        ->and($organization->country)->toEqual($request->get('country'))
        ->and($organization->linkedin_url)->toEqual($request->get('linkedin_url'))
        ->and($organization->facebook_url)->toEqual($request->get('facebook_url'))
        ->and($organization->twitter_url)->toEqual($request->get('twitter_url'));
});
