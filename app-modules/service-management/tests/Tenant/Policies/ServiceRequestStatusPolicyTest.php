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

use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestStatusPeriod;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;

use function Tests\asSuperAdmin;

function referenceServiceRequestStatus(string $reference, ServiceRequestStatus $status): void
{
    match ($reference) {
        'service requests' => ServiceRequest::factory()->for($status, 'status')->create(),
        'a trashed service request' => tap(ServiceRequest::factory()->for($status, 'status')->create())->delete(),
        'a draft service request' => ServiceRequest::factory()->for($status, 'status')->create()->forceFill(['is_draft' => true])->save(),
        'a service request type automated status' => ServiceRequestType::factory()->for($status, 'automatedStatus')->create(),
        'a trashed service request type' => tap(ServiceRequestType::factory()->for($status, 'automatedStatus')->create())->delete(),
        'status periods' => ServiceRequestStatusPeriod::factory()->for($status, 'status')->create(),
        'service request assignments' => createServiceRequestAssignmentForStatus($status),
    };
}

function createServiceRequestAssignmentForStatus(ServiceRequestStatus $status): ServiceRequestAssignment
{
    $type = ServiceRequestType::factory()->create();
    $manager = User::factory()->create();
    $type->managerUsers()->attach($manager);

    $serviceRequest = ServiceRequest::factory()
        ->for(ServiceRequestPriority::factory()->for($type, 'type'), 'priority')
        ->create();

    return ServiceRequestAssignment::factory()
        ->for($serviceRequest, 'serviceRequest')
        ->for($manager, 'user')
        ->for($status, 'serviceRequestStatus')
        ->create();
}

it('allows archiving a service request status that is not in use', function () {
    $user = User::factory()->create();

    asSuperAdmin($user);

    $status = ServiceRequestStatus::factory()->open()->create();

    expect($status->isInUse())->toBeFalse()
        ->and($user->can('archive', $status))->toBeTrue();
});

it('allows archiving a service request status that is in use', function () {
    $user = User::factory()->create();

    asSuperAdmin($user);

    $status = ServiceRequestStatus::factory()->open()->create();

    ServiceRequest::factory()->for($status, 'status')->create();

    expect($status->isInUse())->toBeTrue()
        ->and($user->can('archive', $status))->toBeTrue();
});

it('denies archiving a system protected service request status', function () {
    $user = User::factory()->create();

    asSuperAdmin($user);

    $status = ServiceRequestStatus::factory()->open()->systemProtected()->create();

    expect($user->can('archive', $status))->toBeFalse();
});

it('denies archiving a service request status that is already archived', function () {
    $user = User::factory()->create();

    asSuperAdmin($user);

    $status = ServiceRequestStatus::factory()->archived()->create();

    expect($status->isArchived())->toBeTrue()
        ->and($user->can('archive', $status))->toBeFalse();
});

it('denies deleting a service request status that is in use', function (string $reference) {
    $user = User::factory()->create();

    asSuperAdmin($user);

    $status = ServiceRequestStatus::factory()->open()->create();

    referenceServiceRequestStatus($reference, $status);

    expect($status->isInUse())->toBeTrue()
        ->and($user->can('delete', $status))->toBeFalse()
        ->and($user->can('forceDelete', $status))->toBeFalse();
})->with([
    'service requests',
    'a service request type automated status',
    'service request assignments',
    'status periods',
]);

it('allows deleting a service request status that is not in use', function () {
    $user = User::factory()->create();

    asSuperAdmin($user);

    $status = ServiceRequestStatus::factory()->open()->create();

    expect($user->can('delete', $status))->toBeTrue()
        ->and($user->can('forceDelete', $status))->toBeTrue();
});

it('denies deleting a system protected service request status', function () {
    $user = User::factory()->create();

    asSuperAdmin($user);

    $status = ServiceRequestStatus::factory()->open()->systemProtected()->create();

    expect($user->can('delete', $status))->toBeFalse()
        ->and($user->can('forceDelete', $status))->toBeFalse();
});

it('counts a soft deleted or draft record as still using the status', function (string $reference) {
    $status = ServiceRequestStatus::factory()->open()->create();

    referenceServiceRequestStatus($reference, $status);

    expect($status->isInUse())->toBeTrue();
})->with([
    'a trashed service request',
    'a draft service request',
    'a trashed service request type',
]);
