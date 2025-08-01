<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestStatusResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ListServiceRequestStatuses page', function () {
    ServiceRequestStatus::query()->truncate();

    $serviceRequestStatuses = ServiceRequestStatus::factory()
        ->has(ServiceRequest::factory()->count(fake()->randomNumber(1)), 'serviceRequests')
        ->count(10)
        ->create();

    asSuperAdmin();

    $component = livewire(ServiceRequestStatusResource\Pages\ListServiceRequestStatuses::class);

    $component
        ->assertSuccessful()
        ->assertCanSeeTableRecords($serviceRequestStatuses)
        ->assertCountTableRecords(10)
        ->assertTableColumnExists('service_requests_count');

    $serviceRequestStatuses->each(
        fn (ServiceRequestStatus $serviceRequestType) => $component
            ->assertTableColumnStateSet(
                'id',
                $serviceRequestType->id,
                $serviceRequestType
            )
            ->assertTableColumnStateSet(
                'name',
                $serviceRequestType->name,
                $serviceRequestType
            )
            ->assertTableColumnFormattedStateSet(
                'classification',
                $serviceRequestType->classification->getLabel(),
                $serviceRequestType
            )
            ->assertTableColumnFormattedStateSet(
                'color',
                $serviceRequestType->color->getLabel(),
                $serviceRequestType
            )
        // Currently setting not test for service_request_count as there is no easy way to check now, relying on underlying package tests
    );
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListServiceRequestStatuses is gated with proper access control', function () {
    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('index')
        )->assertSuccessful();
});

test('ListServiceRequestStatuses is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('settings.view-any');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl()
        )->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl()
        )->assertSuccessful();
});
