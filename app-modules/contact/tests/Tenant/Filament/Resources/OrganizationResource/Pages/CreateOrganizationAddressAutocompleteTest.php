<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/
use AidingApp\Contact\Filament\Resources\OrganizationResource\Pages\CreateOrganization;
use App\DataTransferObjects\AutocompletedAddress;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('populates the address fields when a suggestion is selected', function () {
    actingAs(
        User::factory()
            ->create()
            ->givePermissionTo('organization.view-any', 'organization.create')
    );

    $address = new AutocompletedAddress(
        address: '123 Main St',
        city: 'Austin',
        state: 'TX',
        postalCode: '78701',
        country: 'US',
        label: '123 Main St, Austin, TX, 78701, US',
    );

    livewire(CreateOrganization::class)
        ->call('callSchemaComponentMethod', 'form.address', 'reactOnItemSelectedFromJs', [[
            'value' => $address->label,
            'label' => $address->label,
            'data' => ['data' => json_decode(json_encode($address), true)],
        ]])
        ->assertSchemaStateSet([
            'address' => '123 Main St',
            'city' => 'Austin',
            'state' => 'TX',
            'postalcode' => '78701',
            'country' => 'US',
        ]);
});
