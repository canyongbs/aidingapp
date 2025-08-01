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
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestStatusResource\Pages\ViewServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ViewServiceRequestStatus page', function () {
    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestStatusResource::getUrl('view', [
                'record' => $serviceRequestStatus,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Name',
                $serviceRequestStatus->name,
                'Color',
                $serviceRequestStatus->color->getLabel(),
            ]
        );
});

// Permission Tests

test('ViewServiceRequestStatus is gated with proper access control', function () {
    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $contactSource = ServiceRequestStatus::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('view', [
                'record' => $contactSource,
            ])
        )->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.view');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('view', [
                'record' => $contactSource,
            ])
        )->assertSuccessful();
});

test('ViewServiceRequestStatus is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.view');

    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('view', [
                'record' => $serviceRequestStatus,
            ])
        )->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('view', [
                'record' => $serviceRequestStatus,
            ])
        )->assertSuccessful();
});

it('displays the edit action and not the lock when the service request status is not system protected', function () {
    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    asSuperAdmin();

    livewire(ViewServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->assertDontSeeHtml('data-identifier="service_request_type_system_protected"')
        ->assertActionVisible('edit')
        ->assertActionEnabled('edit');
});

it('displays the lock icon rather than the edit action when the service request status is system protected', function () {
    $serviceRequestStatus = ServiceRequestStatus::factory()->create([
        'is_system_protected' => true,
    ]);

    asSuperAdmin();

    livewire(ViewServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->assertSeeHtml('data-identifier="service_request_type_system_protected"')
        ->assertActionHidden('edit')
        ->assertActionDisabled('edit');
});
