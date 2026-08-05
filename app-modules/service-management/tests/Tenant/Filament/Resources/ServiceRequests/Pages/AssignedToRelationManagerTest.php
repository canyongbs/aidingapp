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

use AidingApp\Contact\Models\Contact;
use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\ManageAssignments;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\AssignedToRelationManager;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('Assign To Me action visible when the Service Request is unassigned and the logged-in user belongs to a Department that is Manager of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    $department = Department::factory()->create();

    $user->department()->associate($department)->save();

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerDepartments()->attach($department);

    actingAs($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionVisible('assign-to-me');
});

test('Assign To Me action visible when the Service Request is unassigned and the logged-in user is a direct managerUser of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($user);

    actingAs($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionVisible('assign-to-me');
});

test('Assign To Me action is not visible when the Service Request is already assigned and the logged-in user belongs to a Department that is Manager of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    $department = Department::factory()->create();

    $user->department()->associate($department)->save();

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerDepartments()->attach($department);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    ServiceRequestAssignment::factory()->state([
        'service_request_id' => $serviceRequestsWithManager->getKey(),
        'user_id' => $user->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('assign-to-me');
});

test('Assign To Me action is not visible when the Service Request is already assigned and the logged-in user is a direct managerUser of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    ServiceRequestAssignment::factory()->state([
        'service_request_id' => $serviceRequestsWithManager->getKey(),
        'user_id' => $user->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('assign-to-me');
});

test('Assign To Me action is not visible when the Service Request is unassigned and the logged-in user does not belong to a Department that is Manager of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    $department = Department::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerDepartments()->attach($department);

    actingAs($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('assign-to-me');
});

test('Assign To Me action is not visible when the Service Request is unassigned and the logged-in user is not a direct managerUser of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($otherUser);

    actingAs($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('assign-to-me');
});

test('Manage Assignment action visible when the Service Request is unassigned and the logged-in user belongs to a Department that is Manager of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    $department = Department::factory()->create();

    $user->department()->associate($department)->save();

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerDepartments()->attach($department);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionVisible('manageAssignment');
});

test('Manage Assignment action visible when the Service Request is unassigned and the logged-in user is a direct managerUser of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionVisible('manageAssignment');
});

test('Manage Assignment action is not visible when the logged-in user cannot update the Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($user);

    actingAs($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('manageAssignment');
});

test('Assign To Me action is not visible when the Service Request is Closed and the logged-in user belongs to a Department that is Manager of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    $department = Department::factory()->create();

    $user->department()->associate($department)->save();

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerDepartments()->attach($department);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('assign-to-me');
});

test('Assign To Me action is not visible when the Service Request is Closed and the logged-in user is a direct managerUser of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('assign-to-me');
});

test('Manage Assignment action is not visible when the Service Request is Closed and the logged-in user belongs to a Department that is Manager of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    $department = Department::factory()->create();

    $user->department()->associate($department)->save();

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerDepartments()->attach($department);

    actingAs($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('manageAssignment');
});

test('Manage Assignment action is not visible when the Service Request is Closed and the logged-in user is a direct managerUser of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($user);

    actingAs($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('manageAssignment');
});

test('Manage Assignment action is not visible when the logged-in user is not a manager of the Type of the Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('manageAssignment');
});

test('Manage Assignment action is always labelled "Manage Assignment" when unassigned', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($user);

    actingAs($user);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequest,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionVisible('manageAssignment')
        ->assertTableActionHasLabel('manageAssignment', 'Manage Assignment');
});

test('Manage Assignment action is always labelled "Manage Assignment" when already assigned', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($user);

    actingAs($user);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    ServiceRequestAssignment::factory()->active()->state([
        'service_request_id' => $serviceRequest->getKey(),
        'user_id' => $user->getKey(),
    ])->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequest,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionVisible('manageAssignment')
        ->assertTableActionHasLabel('manageAssignment', 'Manage Assignment');
});

test('Manage Assignment action is hidden and the page renders when the Service Request has no priority or type to assign against', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => null,
    ])->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequest,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertSuccessful()
        ->assertTableActionHidden('manageAssignment');
});

test('submitting Manage Assignment assigns the selected manager and deactivates the prior assignment', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    asSuperAdmin();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $firstManager = User::factory()->create();
    $secondManager = User::factory()->create();
    $serviceRequestType->managerUsers()->attach([$firstManager->getKey(), $secondManager->getKey()]);

    $status = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Open,
    ]);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => $status->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    $priorAssignment = ServiceRequestAssignment::factory()->active()->state([
        'service_request_id' => $serviceRequest->getKey(),
        'user_id' => $firstManager->getKey(),
    ])->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequest,
        'pageClass' => ManageAssignments::class,
    ])
        ->mountTableAction('manageAssignment')
        ->setTableActionData([
            'userId' => $secondManager->getKey(),
            'status_id' => $status->getKey(),
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($serviceRequest->assignments()->where('user_id', $secondManager->getKey())->where('status', ServiceRequestAssignmentStatus::Active)->exists())->toBeTrue();

    expect($priorAssignment->refresh()->status)->toBe(ServiceRequestAssignmentStatus::Inactive);
});

test('submitting Manage Assignment with a different status updates the service request status and snapshots it on the assignment', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    asSuperAdmin();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $manager = User::factory()->create();
    $serviceRequestType->managerUsers()->attach($manager);

    $currentStatus = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Open,
    ]);
    $newStatus = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Open,
    ]);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => $currentStatus->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequest,
        'pageClass' => ManageAssignments::class,
    ])
        ->mountTableAction('manageAssignment')
        ->setTableActionData([
            'userId' => $manager->getKey(),
            'status_id' => $newStatus->getKey(),
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($serviceRequest->refresh()->status_id)->toBe($newStatus->getKey());

    $assignment = $serviceRequest->assignments()->where('user_id', $manager->getKey())->first();

    expect($assignment->service_request_status_id)->toBe($newStatus->getKey());
});

test('submitting Manage Assignment while moving the Service Request to a Closed status stamps the resolution time', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    asSuperAdmin();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $manager = User::factory()->create();
    $serviceRequestType->managerUsers()->attach($manager);

    $currentStatus = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Open,
    ]);
    $closedStatus = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Closed,
    ]);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => $currentStatus->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    // Prime the cached status relation so the update path must resolve the new status, not the stale one.
    $serviceRequest->load('status');

    expect($serviceRequest->time_to_resolution)->toBeNull();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequest,
        'pageClass' => ManageAssignments::class,
    ])
        ->mountTableAction('manageAssignment')
        ->setTableActionData([
            'userId' => $manager->getKey(),
            'status_id' => $closedStatus->getKey(),
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    $serviceRequest->refresh();

    expect($serviceRequest->status_id)->toBe($closedStatus->getKey())
        ->and($serviceRequest->time_to_resolution)->not->toBeNull();
});

test('submitting Manage Assignment with a non-manager user does not create an assignment', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    asSuperAdmin();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $manager = User::factory()->create();
    $serviceRequestType->managerUsers()->attach($manager);

    $nonManager = User::factory()->create();

    $status = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Open,
    ]);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => $status->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequest,
        'pageClass' => ManageAssignments::class,
    ])
        ->mountTableAction('manageAssignment')
        ->setTableActionData([
            'userId' => $nonManager->getKey(),
            'status_id' => $status->getKey(),
        ])
        ->callMountedTableAction()
        ->assertNotified();

    expect($serviceRequest->assignments()->where('user_id', $nonManager->getKey())->exists())->toBeFalse();
});

test('submitting Manage Assignment without a status fails validation and does not create an assignment', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    asSuperAdmin();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $manager = User::factory()->create();
    $serviceRequestType->managerUsers()->attach($manager);

    $status = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Open,
    ]);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => $status->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequest,
        'pageClass' => ManageAssignments::class,
    ])
        ->mountTableAction('manageAssignment')
        ->setTableActionData([
            'userId' => $manager->getKey(),
            'status_id' => null,
        ])
        ->callMountedTableAction()
        ->assertHasTableActionErrors(['status_id' => ['required']]);

    expect($serviceRequest->assignments()->where('user_id', $manager->getKey())->exists())->toBeFalse();
});
