<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
    livewire(EditOrganization::class, [
        'record' => $organization->getRouteKey(),
    ])
        ->assertSuccessful();
});
test('Edit Organization Record', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();
    $organization = Organization::factory()->create();

    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.update');

    actingAs($user);

    $request = collect(EditOrganizationRequestFactory::new()->create());

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
