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

use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\ListServiceRequests;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('it can change status of multiple service requests as super admin', function () {
    asSuperAdmin();

    $newStatus = ServiceRequestStatus::factory()->open()->create();

    $serviceRequests = ServiceRequest::factory()
        ->count(3)
        ->create();

    livewire(ListServiceRequests::class)
        ->assertSuccessful()
        ->assertCountTableRecords($serviceRequests->count())
        ->assertTableBulkActionExists('changeServiceRequestStatus')
        ->callTableBulkAction('changeServiceRequestStatus', $serviceRequests, [
            'statusId' => $newStatus->getKey(),
        ])
        ->assertHasNoTableBulkActionErrors();

    $serviceRequests->each(function (ServiceRequest $serviceRequest) use ($newStatus) {
        expect($serviceRequest->refresh()->status_id)->toBe($newStatus->getKey());
    });
});

test('it can change status of multiple service requests for user directly assigned as manager user', function () {
    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($user);

    $newStatus = ServiceRequestStatus::factory()->open()->create();

    $serviceRequests = ServiceRequest::factory()
        ->state([
            'priority_id' => ServiceRequestPriority::factory()->create([
                'type_id' => $serviceRequestType->getKey(),
            ])->getKey(),
        ])
        ->count(3)
        ->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.update');

    actingAs($user->refresh());

    livewire(ListServiceRequests::class)
        ->assertSuccessful()
        ->assertCountTableRecords($serviceRequests->count())
        ->assertTableBulkActionExists('changeServiceRequestStatus')
        ->callTableBulkAction('changeServiceRequestStatus', $serviceRequests, [
            'statusId' => $newStatus->getKey(),
        ])
        ->assertHasNoTableBulkActionErrors();

    $serviceRequests->each(function (ServiceRequest $serviceRequest) use ($newStatus) {
        expect($serviceRequest->refresh()->status_id)->toBe($newStatus->getKey());
    });
});

test('it can change status of multiple service requests for user belonging to a manager team', function () {
    $user = User::factory()->create();

    $team = Team::factory()->create();

    $user->team()->associate($team)->save();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerTeams()->attach($team);

    $newStatus = ServiceRequestStatus::factory()->open()->create();

    $serviceRequests = ServiceRequest::factory()
        ->state([
            'priority_id' => ServiceRequestPriority::factory()->create([
                'type_id' => $serviceRequestType->getKey(),
            ])->getKey(),
        ])
        ->count(3)
        ->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.update');

    actingAs($user->refresh());

    livewire(ListServiceRequests::class)
        ->assertSuccessful()
        ->assertCountTableRecords($serviceRequests->count())
        ->assertTableBulkActionExists('changeServiceRequestStatus')
        ->callTableBulkAction('changeServiceRequestStatus', $serviceRequests, [
            'statusId' => $newStatus->getKey(),
        ])
        ->assertHasNoTableBulkActionErrors();

    $serviceRequests->each(function (ServiceRequest $serviceRequest) use ($newStatus) {
        expect($serviceRequest->refresh()->status_id)->toBe($newStatus->getKey());
    });
});

test('it cannot change status of service requests for user who is not a manager', function () {
    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $originalStatus = ServiceRequestStatus::factory()->open()->create();
    $newStatus = ServiceRequestStatus::factory()->open()->create();

    $serviceRequests = ServiceRequest::factory()
        ->state([
            'status_id' => $originalStatus->getKey(),
            'priority_id' => ServiceRequestPriority::factory()->create([
                'type_id' => $serviceRequestType->getKey(),
            ])->getKey(),
        ])
        ->count(3)
        ->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.update');

    actingAs($user->refresh());

    livewire(ListServiceRequests::class)
        ->assertSuccessful()
        ->callTableBulkAction('changeServiceRequestStatus', $serviceRequests, [
            'statusId' => $newStatus->getKey(),
        ]);

    $serviceRequests->each(function (ServiceRequest $serviceRequest) use ($originalStatus) {
        expect($serviceRequest->refresh()->status_id)->toBe($originalStatus->getKey());
    });
});

test('it cannot change status of closed service requests', function () {
    asSuperAdmin();

    $closedStatus = ServiceRequestStatus::factory()
        ->state(['classification' => SystemServiceRequestClassification::Closed])
        ->create();

    $newStatus = ServiceRequestStatus::factory()->open()->create();

    $serviceRequests = ServiceRequest::factory()
        ->state([
            'status_id' => $closedStatus->getKey(),
        ])
        ->count(3)
        ->create();

    livewire(ListServiceRequests::class)
        ->assertSuccessful()
        ->callTableBulkAction('changeServiceRequestStatus', $serviceRequests, [
            'statusId' => $newStatus->getKey(),
        ]);

    $serviceRequests->each(function (ServiceRequest $serviceRequest) use ($closedStatus) {
        expect($serviceRequest->refresh()->status_id)->toBe($closedStatus->getKey());
    });
});

test('it cannot change status of service requests without required permissions', function () {
    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($user);

    $originalStatus = ServiceRequestStatus::factory()->open()->create();
    $newStatus = ServiceRequestStatus::factory()->open()->create();

    $serviceRequests = ServiceRequest::factory()
        ->state([
            'status_id' => $originalStatus->getKey(),
            'priority_id' => ServiceRequestPriority::factory()->create([
                'type_id' => $serviceRequestType->getKey(),
            ])->getKey(),
        ])
        ->count(3)
        ->create();

    $user->givePermissionTo('service_request.view-any');

    actingAs($user->refresh());

    livewire(ListServiceRequests::class)
        ->assertSuccessful()
        ->callTableBulkAction('changeServiceRequestStatus', $serviceRequests, [
            'statusId' => $newStatus->getKey(),
        ]);

    $serviceRequests->each(function (ServiceRequest $serviceRequest) use ($originalStatus) {
        expect($serviceRequest->refresh()->status_id)->toBe($originalStatus->getKey());
    });
});
