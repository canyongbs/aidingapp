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

use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\ViewServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

// Authorization

test('editPriority action is visible for manager department member with update permission', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create();
    $user->department()->associate($department)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managerDepartments()->attach($department);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');
    $user->givePermissionTo('service_request.*.update');

    actingAs($user->refresh());

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible(TestAction::make('editPriority')->schemaComponent('priority.name'));
});

test('editPriority action is hidden for user without update permission', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create();
    $user->department()->associate($department)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managerDepartments()->attach($department);
    $serviceRequestType->auditorDepartments()->attach($department);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    actingAs($user->refresh());

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionHidden(TestAction::make('editPriority')->schemaComponent('priority.name'));
});

test('editPriority action is hidden when the service request has no priority', function () {
    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => null,
    ])->create();

    asSuperAdmin();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionHidden(TestAction::make('editPriority')->schemaComponent('priority.name'));
});

// Validation

test('editPriority requires priority_id', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    asSuperAdmin();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->callAction(TestAction::make('editPriority')->schemaComponent('priority.name'), data: [
            'priority_id' => null,
        ])
        ->assertHasFormErrors(['priority_id' => 'required']);
});

// Success

test('can update the service request priority', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    $originalPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $serviceRequestType->getKey(),
    ]);

    $newPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $serviceRequestType->getKey(),
    ]);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => $originalPriority->getKey(),
    ])->create();

    asSuperAdmin();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->callAction(TestAction::make('editPriority')->schemaComponent('priority.name'), data: [
            'priority_id' => $newPriority->getKey(),
        ])
        ->assertHasNoFormErrors()
        ->assertNotified();

    expect($serviceRequest->fresh()->priority_id)->toBe($newPriority->getKey());
});
