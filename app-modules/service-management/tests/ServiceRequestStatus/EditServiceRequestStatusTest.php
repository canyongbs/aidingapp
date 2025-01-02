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
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestStatusResource\Pages\EditServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Tests\RequestFactories\EditServiceRequestStatusRequestFactory;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Validation\Rules\Enum;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;
use function Tests\asSuperAdmin;

test('A successful action on the EditServiceRequestStatus page', function () {
    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditServiceRequestStatusRequestFactory::new()->create();

    livewire(EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'classification' => $serviceRequestStatus->classification->value,
            'name' => $serviceRequestStatus->name,
            'color' => $serviceRequestStatus->color->value,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $serviceRequestStatus->fresh()->name);
    assertEquals($editRequest['classification'], $serviceRequestStatus->fresh()->classification);
    assertEquals($editRequest['color'], $serviceRequestStatus->fresh()->color);
});

test('EditServiceRequestStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    livewire(EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'classification' => $serviceRequestStatus->classification->value,
            'name' => $serviceRequestStatus->name,
            'color' => $serviceRequestStatus->color->value,
        ])
        ->fillForm(EditServiceRequestStatusRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequestStatus::class, $serviceRequestStatus->toArray());
})->with(
    [
        'name missing' => [EditServiceRequestStatusRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditServiceRequestStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [EditServiceRequestStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [EditServiceRequestStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

// Permission Tests

test('EditServiceRequestStatus is gated with proper access control', function () {
    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus,
            ])
        )->assertForbidden();

    livewire(EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestStatusRequestFactory::new()->create());

    livewire(EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $serviceRequestStatus->fresh()->name);
});

test('EditServiceRequestStatus is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    $serviceRequestStatus = ServiceRequestStatus::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus,
            ])
        )->assertForbidden();

    livewire(EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestStatusRequestFactory::new()->create());

    livewire(EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $serviceRequestStatus->fresh()->name);
});

test('EditServiceRequestStatus is gated with proper system protection access control', function () {
    /** @var ServiceRequestStatus $serviceRequestStatus */
    $serviceRequestStatus = ServiceRequestStatus::factory()
        ->systemProtected()
        ->create();

    asSuperAdmin()
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus,
            ])
        )->assertForbidden();

    livewire(EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->assertForbidden();

    $serviceRequestStatus = ServiceRequestStatus::factory()
        ->create();

    asSuperAdmin()
        ->get(
            ServiceRequestStatusResource::getUrl('edit', [
                'record' => $serviceRequestStatus,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestStatusRequestFactory::new()->create());

    livewire(EditServiceRequestStatus::class, [
        'record' => $serviceRequestStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $serviceRequestStatus->fresh()->name);
});
