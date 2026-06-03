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

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\Pages\ManageServiceRequestTypePriorities;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\CreateServiceRequestPriorityRequestFactory;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\EditServiceRequestPriorityRequestFactory;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('A successful action on the ManageServiceRequestTypePriorities page', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestTypeResource::getUrl('service-request-type-priorities', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )
        ->assertSuccessful();
});

it('can create a service request priority', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    $createRequest = CreateServiceRequestPriorityRequestFactory::new()->create();

    asSuperAdmin();

    livewire(ManageServiceRequestTypePriorities::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->callTableAction('create', data: $createRequest)
        ->assertHasNoTableActionErrors();

    expect(
        ServiceRequestPriority::query()
            ->where('type_id', $serviceRequestType->getKey())
            ->where('name', $createRequest['name'])
            ->where('order', $createRequest['order'])
            ->exists()
    )->toBeTrue();
});

it('can edit a service request priority', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    $priority = ServiceRequestPriority::factory()
        ->for($serviceRequestType, 'type')
        ->create();

    $editRequest = EditServiceRequestPriorityRequestFactory::new()->create();

    asSuperAdmin();

    livewire(ManageServiceRequestTypePriorities::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->callTableAction('edit', record: $priority, data: $editRequest)
        ->assertHasNoTableActionErrors();

    expect($priority->refresh()->name)->toBe($editRequest['name']);
});

it('can delete a service request priority', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    $priority = ServiceRequestPriority::factory()
        ->for($serviceRequestType, 'type')
        ->create();

    asSuperAdmin();

    livewire(ManageServiceRequestTypePriorities::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->callTableAction('delete', record: $priority->getKey())
        ->assertHasNoTableActionErrors();

    assertSoftDeleted(ServiceRequestPriority::class, ['id' => $priority->getKey()]);
});

it('validates required fields when creating a service request priority', function (array $data, array $errors) {
    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin();

    livewire(ManageServiceRequestTypePriorities::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->callTableAction('create', data: $data)
        ->assertHasTableActionErrors($errors);
})->with([
    'name missing' => [['name' => null, 'order' => 1], ['name' => 'required']],
    'name not a string' => [['name' => 123, 'order' => 1], ['name' => 'string']],
    'order missing' => [['name' => 'Test Priority', 'order' => null], ['order' => 'required']],
    'order not an integer' => [['name' => 'Test Priority', 'order' => 'abc'], ['order' => 'integer']],
]);

it('validates unique name scoped to type when creating a service request priority', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    $existingPriority = ServiceRequestPriority::factory()
        ->for($serviceRequestType, 'type')
        ->create();

    asSuperAdmin();

    livewire(ManageServiceRequestTypePriorities::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->callTableAction('create', data: [
            'name' => $existingPriority->name,
            'order' => $existingPriority->order + 1,
        ])
        ->assertHasTableActionErrors(['name' => 'unique']);
});

it('validates required fields when editing a service request priority', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    $priority = ServiceRequestPriority::factory()
        ->for($serviceRequestType, 'type')
        ->create();

    asSuperAdmin();

    livewire(ManageServiceRequestTypePriorities::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->callTableAction('edit', record: $priority, data: ['name' => null])
        ->assertHasTableActionErrors(['name' => 'required']);
});

test('ManageServiceRequestTypePriorities is gated with proper access control', function () {
    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('service-request-type-priorities', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )->assertForbidden();

    livewire(ManageServiceRequestTypePriorities::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.view');
    $user->givePermissionTo('service_request_priority.view-any');

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('service-request-type-priorities', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )->assertSuccessful();
});

test('ManageServiceRequestTypePriorities is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.view');
    $user->givePermissionTo('service_request_priority.view-any');

    $serviceRequestType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('service-request-type-priorities', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )->assertForbidden();

    livewire(ManageServiceRequestTypePriorities::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('service-request-type-priorities', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )->assertSuccessful();
});
