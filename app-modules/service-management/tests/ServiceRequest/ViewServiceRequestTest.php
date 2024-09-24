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

use function Tests\asSuperAdmin;

use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageAssignments;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ViewServiceRequest;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageServiceRequestUpdate;

test('The correct details are displayed on the ViewServiceRequest page', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $serviceRequest,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Service Request Number',
                $serviceRequest->service_request_number,
                'Division',
                $serviceRequest->division->name,
                'Status',
                $serviceRequest->status->name,
                'Priority',
                $serviceRequest->priority->name,
                'Type',
                $serviceRequest->priority->type->name,
                'Description',
                $serviceRequest->close_details,
                'Internal Details',
                $serviceRequest->res_details,
            ]
        );
});

// Permission Tests

test('ViewServiceRequest is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $serviceRequest = ServiceRequest::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $serviceRequest,
            ])
        )->assertForbidden();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $serviceRequest,
            ])
        )->assertSuccessful();
});

test('ViewServiceRequest is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    $serviceRequest = ServiceRequest::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $serviceRequest,
            ])
        )->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $serviceRequest,
            ])
        )->assertSuccessful();
});

test('service request lock icon is shown when status classification closed', function (string $pages) {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');
    $user->givePermissionTo('service_request_assignment.view-any');
    $user->givePermissionTo('service_request_update.view-any');

    actingAs($user);

    $serviceRequest = ServiceRequest::factory([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->id,
    ])->create();

    livewire($pages, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSeeHtml('data-identifier="service_request_closed"');
})
    ->with([
        ViewServiceRequest::class,
        ManageAssignments::class,
        ManageServiceRequestUpdate::class,
    ]);
