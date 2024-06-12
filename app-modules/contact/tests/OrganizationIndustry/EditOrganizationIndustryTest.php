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
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource\Pages\EditOrganizationIndustry;
use AidingApp\Contact\Tests\OrganizationIndustry\RequestFactories\EditOrganizationIndustryRequestFactory;

test('Edit Organization Industry is gated with proper access control', function () {
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
    livewire(EditOrganizationIndustry::class, [
        'record' => $organization_industry->getRouteKey(),
    ])
        ->assertSuccessful();
});
test('Edit Organization Industry Record', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();
    $organization_industry = OrganizationIndustry::factory()->create();

    $user->givePermissionTo('organization_industry.view-any');
    $user->givePermissionTo('organization_industry.*.update');

    actingAs($user);

    $request = collect(EditOrganizationIndustryRequestFactory::new()->create());

    livewire(EditOrganizationIndustry::class, [
        'record' => $organization_industry->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    $organization_industry->refresh();

    expect($organization_industry->name)->toEqual($request->get('name'));
});
