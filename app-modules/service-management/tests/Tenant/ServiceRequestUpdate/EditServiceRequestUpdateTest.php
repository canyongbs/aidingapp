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

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource\Pages\EditServiceRequestUpdate;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\EditServiceRequestUpdateRequestFactory;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Validation\Rules\Enum;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;
use function Tests\asSuperAdmin;

test('A successful action on the EditServiceRequestUpdate page', function () {
    $serviceRequest = ServiceRequest::factory([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
    ]);

    $serviceRequestUpdate = ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create();

    asSuperAdmin()
        ->get(
            ServiceRequestUpdateResource::getUrl('edit', [
                'record' => $serviceRequestUpdate->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $request = collect(EditServiceRequestUpdateRequestFactory::new()->create());

    livewire(EditServiceRequestUpdate::class, [
        'record' => $serviceRequestUpdate->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ServiceRequestUpdate::class, $request->except('service_request_id')->toArray());

    expect(ServiceRequestUpdate::first()->serviceRequest->id)
        ->toEqual($request->get('service_request_id'));
});

test('EditServiceRequestUpdate requires valid data', function ($data, $errors) {
    $serviceRequest = ServiceRequest::factory([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
    ]);

    $serviceRequestUpdate = ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create();

    asSuperAdmin();

    livewire(EditServiceRequestUpdate::class, [
        'record' => $serviceRequestUpdate->getRouteKey(),
    ])
        ->fillForm(EditServiceRequestUpdateRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    unset($serviceRequestUpdate->serviceRequest);

    assertDatabaseHas(ServiceRequestUpdate::class, $serviceRequestUpdate->toArray());

    expect(ServiceRequestUpdate::first()->serviceRequest->id)
        ->toEqual($serviceRequestUpdate->serviceRequest->id);
})->with(
    [
        'service_request missing' => [EditServiceRequestUpdateRequestFactory::new()->state(['service_request_id' => null]), ['service_request_id' => 'required']],
        'service_request not existing service_request id' => [EditServiceRequestUpdateRequestFactory::new()->state(['service_request_id' => fake()->uuid()]), ['service_request_id' => 'exists']],
        'update missing' => [EditServiceRequestUpdateRequestFactory::new()->state(['update' => null]), ['update' => 'required']],
        'update is not a string' => [EditServiceRequestUpdateRequestFactory::new()->state(['update' => 99]), ['update' => 'string']],
        'direction missing' => [EditServiceRequestUpdateRequestFactory::new()->state(['direction' => null]), ['direction' => 'required']],
        'direction not a valid enum' => [EditServiceRequestUpdateRequestFactory::new()->state(['direction' => 'invalid']), ['direction' => Enum::class]],
        'internal not a boolean' => [EditServiceRequestUpdateRequestFactory::new()->state(['internal' => 'invalid']), ['internal' => 'boolean']],
    ]
);

// Permission Tests

test('EditServiceRequestUpdate is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $serviceRequest = ServiceRequest::factory([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
    ]);

    $serviceRequestUpdate = ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create();

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('edit', [
                'record' => $serviceRequestUpdate,
            ])
        )->assertForbidden();

    livewire(EditServiceRequestUpdate::class, [
        'record' => $serviceRequestUpdate->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('service_request_update.view-any');
    $user->givePermissionTo('service_request_update.*.update');

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('edit', [
                'record' => $serviceRequestUpdate,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestUpdateRequestFactory::new()->create());

    livewire(EditServiceRequestUpdate::class, [
        'record' => $serviceRequestUpdate->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ServiceRequestUpdate::class, $request->except('service_request_id')->toArray());

    expect(ServiceRequestUpdate::first()->serviceRequest->id)
        ->toEqual($request->get('service_request_id'));
});

test('EditServiceRequestUpdate is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('service_request_update.view-any');
    $user->givePermissionTo('service_request_update.*.update');

    $serviceRequest = ServiceRequest::factory([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
    ]);

    $serviceRequestUpdate = ServiceRequestUpdate::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->create();

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('edit', [
                'record' => $serviceRequestUpdate,
            ])
        )->assertForbidden();

    livewire(EditServiceRequestUpdate::class, [
        'record' => $serviceRequestUpdate->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('edit', [
                'record' => $serviceRequestUpdate,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestUpdateRequestFactory::new()->create());

    livewire(EditServiceRequestUpdate::class, [
        'record' => $serviceRequestUpdate->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['update'], $serviceRequestUpdate->fresh()->update);
});
