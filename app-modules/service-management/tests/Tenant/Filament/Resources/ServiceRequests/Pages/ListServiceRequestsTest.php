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

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ContactServiceManagement;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\ServiceRequestsRelationManager;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Enums\ServiceRequestCategory;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\ListServiceRequests;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ListServiceRequests page', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    $department = Department::factory()->create();

    $user->department()->associate($department)->save();

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerDepartments()->attach($department);

    $serviceRequests = ServiceRequest::factory()
        ->has(
            factory: ServiceRequestAssignment::factory()
                ->state([
                    'user_id' => $user->getKey(),
                ])
                ->count(1)
                ->active(),
            relationship: 'assignments'
        )
        ->state([
            'priority_id' => ServiceRequestPriority::factory()->create([
                'type_id' => $serviceRequestType->getKey(),
            ])->getKey(),
        ])
        ->count(10)
        ->create();

    $component = livewire(ListServiceRequests::class);

    $component->assertSuccessful()
        ->assertCanSeeTableRecords($serviceRequests)
        ->assertCountTableRecords(10);

    $component
        ->assertTableColumnExists('service_request_number')
        ->assertTableColumnExists('priority.type.name')
        ->assertTableColumnExists('related_to')
        ->assertTableColumnExists('division.name')
        ->assertTableColumnExists('sla')
        ->assertTableColumnExists('feedback_summary')
        ->assertTableColumnExists('dates')
        ->assertSee('Manager:')
        ->assertSee('Customer:')
        ->assertSee('Response:')
        ->assertSee('Resolution:')
        ->assertSee('Created:')
        ->assertSee('Updated:')
        ->assertSee('Unassigned')
        ->assertSeeInOrder(['CSAT:', 'N/A', 'NPS:', 'N/A']);

    $serviceRequests->each(
        fn (ServiceRequest $serviceRequest) => $component
            ->assertTableColumnStateSet(
                'service_request_number',
                $serviceRequest->service_request_number,
                $serviceRequest
            )
            ->assertTableColumnStateSet(
                'division.name',
                $serviceRequest->division->name,
                $serviceRequest
            )
            ->assertSee($serviceRequest->service_request_number)
            ->assertSee($serviceRequest->category->getLabel())
            ->assertSee($serviceRequest->status->name)
            ->assertSee($serviceRequest->respondent->full_name)
            ->assertSee($serviceRequest->created_at->format('m-d-Y') . ' (0 days)')
            ->assertSee($serviceRequest->updated_at->format('m-d-Y') . ' (0 days)')
    );
});

test('The correct details are displayed on the ListServiceRequests page via direct user manager', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($user);

    $serviceRequests = ServiceRequest::factory()
        ->has(
            factory: ServiceRequestAssignment::factory()
                ->state([
                    'user_id' => $user->getKey(),
                ])
                ->count(1)
                ->active(),
            relationship: 'assignments'
        )
        ->state([
            'priority_id' => ServiceRequestPriority::factory()->create([
                'type_id' => $serviceRequestType->getKey(),
            ])->getKey(),
        ])
        ->count(10)
        ->create();

    $component = livewire(ListServiceRequests::class);

    $component->assertSuccessful()
        ->assertCanSeeTableRecords($serviceRequests)
        ->assertCountTableRecords(10);

    $component
        ->assertTableColumnExists('service_request_number')
        ->assertTableColumnExists('priority.type.name')
        ->assertTableColumnExists('related_to')
        ->assertTableColumnExists('division.name')
        ->assertTableColumnExists('sla')
        ->assertTableColumnExists('feedback_summary')
        ->assertTableColumnExists('dates')
        ->assertSee('Manager:')
        ->assertSee('Customer:')
        ->assertSee('Response:')
        ->assertSee('Resolution:')
        ->assertSee('Created:')
        ->assertSee('Updated:')
        ->assertSee($user->name)
        ->assertSeeInOrder(['CSAT:', 'N/A', 'NPS:', 'N/A']);

    $serviceRequests->each(
        fn (ServiceRequest $serviceRequest) => $component
            ->assertTableColumnStateSet(
                'service_request_number',
                $serviceRequest->service_request_number,
                $serviceRequest
            )
            ->assertTableColumnStateSet(
                'division.name',
                $serviceRequest->division->name,
                $serviceRequest
            )
            ->assertSee($serviceRequest->service_request_number)
            ->assertSee($serviceRequest->category->getLabel())
            ->assertSee($serviceRequest->status->name)
            ->assertSee($serviceRequest->respondent->full_name)
            ->assertSee($serviceRequest->created_at->format('m-d-Y') . ' (0 days)')
            ->assertSee($serviceRequest->updated_at->format('m-d-Y') . ' (0 days)')
    );
});

test('category is rendered inline with the service request number on the ListServiceRequests page', function () {
    asSuperAdmin();

    $serviceRequest = ServiceRequest::factory()->create([
        'category' => ServiceRequestCategory::Incident,
    ]);

    livewire(ListServiceRequests::class)
        ->assertSuccessful()
        ->assertTableColumnExists('service_request_number')
        ->assertSee($serviceRequest->service_request_number)
        ->assertSee(ServiceRequestCategory::Incident->getLabel());
});

test('can filter service request by category', function () {
    asSuperAdmin();

    $incidentServiceRequests = ServiceRequest::factory()
        ->count(2)
        ->create([
            'category' => ServiceRequestCategory::Incident,
        ]);

    $requestServiceRequests = ServiceRequest::factory()
        ->count(2)
        ->create([
            'category' => ServiceRequestCategory::Request,
        ]);

    livewire(ListServiceRequests::class)
        ->assertCanSeeTableRecords($incidentServiceRequests->merge($requestServiceRequests))
        ->filterTable('category', ServiceRequestCategory::Incident->value)
        ->assertCanSeeTableRecords($incidentServiceRequests)
        ->assertCanNotSeeTableRecords($requestServiceRequests);
});

test('can sort service requests by type', function () {
    asSuperAdmin();

    $firstType = ServiceRequestType::factory()->create(['name' => 'Alpha Type']);
    $secondType = ServiceRequestType::factory()->create(['name' => 'Beta Type']);

    $firstPriority = ServiceRequestPriority::factory()->create(['type_id' => $firstType->getKey()]);
    $secondPriority = ServiceRequestPriority::factory()->create(['type_id' => $secondType->getKey()]);

    $firstServiceRequest = ServiceRequest::factory()->create(['priority_id' => $firstPriority->getKey()]);
    $secondServiceRequest = ServiceRequest::factory()->create(['priority_id' => $secondPriority->getKey()]);

    livewire(ListServiceRequests::class)
        ->sortTable('priority.type.name', 'asc')
        ->assertCanSeeTableRecords([$firstServiceRequest, $secondServiceRequest], inOrder: true)
        ->sortTable('priority.type.name', 'desc')
        ->assertCanSeeTableRecords([$secondServiceRequest, $firstServiceRequest], inOrder: true);
});

test('type column displays the correct service request type name', function () {
    asSuperAdmin();

    $serviceRequestType = ServiceRequestType::factory()->create(['name' => 'Test Type']);

    $priority = ServiceRequestPriority::factory()->create(['type_id' => $serviceRequestType->getKey()]);

    $serviceRequest = ServiceRequest::factory()->create(['priority_id' => $priority->getKey()]);

    livewire(ListServiceRequests::class)
        ->assertTableColumnStateSet(
            'priority.type.name',
            'Test Type',
            $serviceRequest
        );
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListServiceRequests is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('service_request.view-any');

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('index')
        )->assertSuccessful();
});

test('ListServiceRequests is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.view-any');

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl()
        )->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl()
        )->assertSuccessful();
});

test('can filter service request by organization', function () {
    asSuperAdmin();

    $organizations = Organization::factory()->count(10)->create();

    $organization = $organizations->first();

    $serviceRequestsInOrganization = ServiceRequest::factory()
        ->count(3)
        ->for(Contact::factory()->state(['organization_id' => $organization->id]), 'respondent')
        ->create();

    $serviceRequestsNotInOrganization = ServiceRequest::factory()
        ->count(3)
        ->create();

    livewire(ListServiceRequests::class)
        ->assertCanSeeTableRecords($serviceRequestsInOrganization->merge($serviceRequestsNotInOrganization))
        ->filterTable('organization', $organization->id)
        ->assertCanSeeTableRecords(
            $serviceRequestsInOrganization
        )
        ->assertCanNotSeeTableRecords($serviceRequestsNotInOrganization);
});

test('service requests only visible to service request type managers', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.view-any');

    $department = Department::factory()->create();

    $user->department()->associate($department)->save();

    $user->refresh();

    actingAs($user);

    $serviceRequests = ServiceRequest::factory()
        ->count(3)
        ->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerDepartments()->attach($department);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->count(3)
        ->create();

    livewire(ListServiceRequests::class)
        ->assertCanSeeTableRecords(
            $serviceRequestsWithManager
        )
        ->assertCanNotSeeTableRecords($serviceRequests);
});

test('service requests only visible to direct user service request type managers', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.view-any');

    actingAs($user);

    $serviceRequests = ServiceRequest::factory()
        ->count(3)
        ->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->count(3)
        ->create();

    livewire(ListServiceRequests::class)
        ->assertCanSeeTableRecords(
            $serviceRequestsWithManager
        )
        ->assertCanNotSeeTableRecords($serviceRequests);
});

test('service requests only visible to service request type auditors', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.view-any');

    $department = Department::factory()->create();

    $user->department()->associate($department)->save();

    $user->refresh();

    actingAs($user);

    $serviceRequests = ServiceRequest::factory()
        ->count(3)
        ->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->auditorDepartments()->attach($department);

    $serviceRequestsWithAuditors = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->count(3)
        ->create();

    livewire(ListServiceRequests::class)
        ->assertCanSeeTableRecords(
            $serviceRequestsWithAuditors
        )
        ->assertCanNotSeeTableRecords($serviceRequests);
});

test('service requests only visible to direct user service request type auditors', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.view-any');

    actingAs($user);

    $serviceRequests = ServiceRequest::factory()
        ->count(3)
        ->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->auditorUsers()->attach($user);

    $serviceRequestsWithAuditors = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->count(3)
        ->create();

    livewire(ListServiceRequests::class)
        ->assertCanSeeTableRecords(
            $serviceRequestsWithAuditors
        )
        ->assertCanNotSeeTableRecords($serviceRequests);
});

test('can list audit member to service request type', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create();
    $user->department()->associate($department)->save();
    $user->refresh();

    $contact = Contact::factory()->create();

    $serviceRequestsWithoutManager = ServiceRequest::factory()
        ->for($contact, 'respondent')
        ->count(3)
        ->create();

    $serviceRequests = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->for(ServiceRequestType::factory()
            ->hasAttached($department, [], 'managerDepartments'), 'type'),
    ])
        ->for($contact, 'respondent')
        ->count(3)
        ->create();

    actingAs($user)
        ->get(
            ContactResource::getUrl('service-management', [
                'record' => $contact->getRouteKey(),
            ])
        )->assertForbidden();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.create');
    $user->givePermissionTo('department.view-any');
    $user->givePermissionTo('contact.view-any');

    actingAs($user);

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
        ->assertCanSeeTableRecords($serviceRequests)
        ->assertCanNotSeeTableRecords($serviceRequestsWithoutManager);
});

test('can list direct user manager to service request type', function () {
    $user = User::factory()->create();

    $contact = Contact::factory()->create();

    $serviceRequestsWithoutManager = ServiceRequest::factory()
        ->for($contact, 'respondent')
        ->count(3)
        ->create();

    $serviceRequests = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->for(ServiceRequestType::factory()
            ->hasAttached($user, [], 'managerUsers'), 'type'),
    ])
        ->for($contact, 'respondent')
        ->count(3)
        ->create();

    actingAs($user)
        ->get(
            ContactResource::getUrl('service-management', [
                'record' => $contact->getRouteKey(),
            ])
        )->assertForbidden();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.create');
    $user->givePermissionTo('department.view-any');
    $user->givePermissionTo('contact.view-any');

    actingAs($user);

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
        ->assertCanSeeTableRecords($serviceRequests)
        ->assertCanNotSeeTableRecords($serviceRequestsWithoutManager);
});

it('can filter service requests by assigned to with unassigned option', function () {
    $unassignedRequest = ServiceRequest::factory()->create();

    $user = User::factory()->create();

    $secondUser = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    $department = Department::factory()->create();

    $user->department()->associate($department)->save();

    $secondUser->department()->associate($department)->save();

    $user->refresh();

    $secondUser->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerDepartments()->attach($department);

    asSuperAdmin();

    $assignedRequest = ServiceRequest::factory()
        ->has(
            factory: ServiceRequestAssignment::factory()
                ->state([
                    'user_id' => $user->getKey(),
                ])
                ->active(),
            relationship: 'assignments'
        )
        ->state([
            'priority_id' => ServiceRequestPriority::factory()->create([
                'type_id' => $serviceRequestType->getKey(),
            ])->getKey(),
        ])
        ->create();

    $assignedSecondRequest = ServiceRequest::factory()
        ->has(
            factory: ServiceRequestAssignment::factory()
                ->state([
                    'user_id' => $user->getKey(),
                ])
                ->active(),
            relationship: 'assignments'
        )
        ->state([
            'priority_id' => ServiceRequestPriority::factory()->create([
                'type_id' => $serviceRequestType->getKey(),
            ])->getKey(),
        ])
        ->create();

    ServiceRequestAssignment::factory()->state([
        'service_request_id' => $assignedSecondRequest->getKey(),
        'user_id' => $secondUser->getKey(),
    ])
        ->create();

    livewire(ListServiceRequests::class)
        ->assertCanSeeTableRecords([
            $unassignedRequest,
            $assignedRequest,
            $assignedSecondRequest,
        ])
        ->filterTable('assignedTo', 'unassigned')
        ->assertCanSeeTableRecords([$unassignedRequest])
        ->assertCanNotSeeTableRecords([
            $assignedRequest,
            $assignedSecondRequest,
        ])
        ->removeTableFilter('assignedTo')
        ->filterTable('assignedTo', $user->getKey())
        ->assertCanSeeTableRecords([$assignedRequest])
        ->assertCanNotSeeTableRecords([
            $unassignedRequest,
            $assignedSecondRequest,
        ])
        ->removeTableFilter('assignedTo')
        ->assertCanSeeTableRecords([
            $unassignedRequest,
            $assignedRequest,
            $assignedSecondRequest,
        ]);
});

it('can filter service requests by assigned to with unassigned option via direct user manager', function () {
    $unassignedRequest = ServiceRequest::factory()->create();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.*.update');

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($user);

    asSuperAdmin();

    $assignedRequest = ServiceRequest::factory()
        ->has(
            factory: ServiceRequestAssignment::factory()
                ->state([
                    'user_id' => $user->getKey(),
                ])
                ->active(),
            relationship: 'assignments'
        )
        ->state([
            'priority_id' => ServiceRequestPriority::factory()->create([
                'type_id' => $serviceRequestType->getKey(),
            ])->getKey(),
        ])
        ->create();

    livewire(ListServiceRequests::class)
        ->assertCanSeeTableRecords([
            $unassignedRequest,
            $assignedRequest,
        ])
        ->filterTable('assignedTo', 'unassigned')
        ->assertCanSeeTableRecords([$unassignedRequest])
        ->assertCanNotSeeTableRecords([
            $assignedRequest,
        ])
        ->removeTableFilter('assignedTo')
        ->filterTable('assignedTo', $user->getKey())
        ->assertCanSeeTableRecords([$assignedRequest])
        ->assertCanNotSeeTableRecords([
            $unassignedRequest,
        ])
        ->removeTableFilter('assignedTo')
        ->assertCanSeeTableRecords([
            $unassignedRequest,
            $assignedRequest,
        ]);
});

it('can filter service requests by searched assigned user outside initial preload options', function () {
    asSuperAdmin();

    User::factory()->count(75)->create();

    $searchedUser = User::factory()->create();
    $otherUser = User::factory()->create();

    $searchedUserDepartment = Department::factory()->create();
    $otherUserDepartment = Department::factory()->create();

    $searchedUser->department()->associate($searchedUserDepartment)->save();
    $otherUser->department()->associate($otherUserDepartment)->save();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerDepartments()->attach([$searchedUserDepartment->getKey(), $otherUserDepartment->getKey()]);

    $priority = ServiceRequestPriority::factory()->create([
        'type_id' => $serviceRequestType->getKey(),
    ]);

    $matchingRequest = ServiceRequest::factory()
        ->has(
            factory: ServiceRequestAssignment::factory()
                ->state([
                    'user_id' => $searchedUser->getKey(),
                ])
                ->active(),
            relationship: 'assignments'
        )
        ->state([
            'priority_id' => $priority->getKey(),
        ])
        ->create();

    $nonMatchingRequest = ServiceRequest::factory()
        ->has(
            factory: ServiceRequestAssignment::factory()
                ->state([
                    'user_id' => $otherUser->getKey(),
                ])
                ->active(),
            relationship: 'assignments'
        )
        ->state([
            'priority_id' => $priority->getKey(),
        ])
        ->create();

    livewire(ListServiceRequests::class)
        ->filterTable('assignedTo', $searchedUser->getKey())
        ->assertHasNoErrors()
        ->assertCanSeeTableRecords([$matchingRequest])
        ->assertCanNotSeeTableRecords([$nonMatchingRequest]);
});

it('default non closed service request will not display', function () {
    $nonClosedServiceRequests = ServiceRequest::factory()
        ->for(
            ServiceRequestStatus::factory()
                ->state(['classification' => SystemServiceRequestClassification::Open]),
            'status'
        )
        ->count(3)
        ->create();

    $closedServiceRequests = ServiceRequest::factory()
        ->for(
            ServiceRequestStatus::factory()
                ->state(['classification' => SystemServiceRequestClassification::Closed]),
            'status'
        )
        ->count(3)
        ->create();

    asSuperAdmin();

    livewire(ListServiceRequests::class)
        ->assertCanSeeTableRecords($nonClosedServiceRequests)
        ->assertCanNotSeeTableRecords($closedServiceRequests)
        ->removeTableFilter('status')
        ->assertCanSeeTableRecords($nonClosedServiceRequests->merge($closedServiceRequests));
});

it('can filter service requests by type', function () {
    asSuperAdmin();

    $typeA = ServiceRequestType::factory()->create();
    $typeB = ServiceRequestType::factory()->create();

    $priorityA = ServiceRequestPriority::factory()->create([
        'type_id' => $typeA->getKey(),
    ]);

    $priorityB = ServiceRequestPriority::factory()->create([
        'type_id' => $typeB->getKey(),
    ]);

    $serviceRequestsTypeA = ServiceRequest::factory()
        ->count(2)
        ->create(['priority_id' => $priorityA->getKey()]);

    $serviceRequestsTypeB = ServiceRequest::factory()
        ->count(2)
        ->create(['priority_id' => $priorityB->getKey()]);

    livewire(ListServiceRequests::class)
        ->assertCanSeeTableRecords($serviceRequestsTypeA->merge($serviceRequestsTypeB))
        ->filterTable('type', ['types' => [$typeA->getKey()]])
        ->assertCanSeeTableRecords($serviceRequestsTypeA)
        ->assertCanNotSeeTableRecords($serviceRequestsTypeB);
});

it('can filter service requests by multiple types', function () {
    asSuperAdmin();

    $typeA = ServiceRequestType::factory()->create();
    $typeB = ServiceRequestType::factory()->create();
    $typeC = ServiceRequestType::factory()->create();

    $priorityA = ServiceRequestPriority::factory()->create([
        'type_id' => $typeA->getKey(),
    ]);

    $priorityB = ServiceRequestPriority::factory()->create([
        'type_id' => $typeB->getKey(),
    ]);

    $priorityC = ServiceRequestPriority::factory()->create([
        'type_id' => $typeC->getKey(),
    ]);

    $serviceRequestsTypeA = ServiceRequest::factory()
        ->count(2)
        ->create(['priority_id' => $priorityA->getKey()]);

    $serviceRequestsTypeB = ServiceRequest::factory()
        ->count(2)
        ->create(['priority_id' => $priorityB->getKey()]);

    $serviceRequestsTypeC = ServiceRequest::factory()
        ->count(2)
        ->create(['priority_id' => $priorityC->getKey()]);

    livewire(ListServiceRequests::class)
        ->assertCanSeeTableRecords($serviceRequestsTypeA->merge($serviceRequestsTypeB)->merge($serviceRequestsTypeC))
        ->filterTable('type', ['types' => [$typeA->getKey(), $typeB->getKey()]])
        ->assertCanSeeTableRecords($serviceRequestsTypeA->merge($serviceRequestsTypeB))
        ->assertCanNotSeeTableRecords($serviceRequestsTypeC);
});

it('shows all service requests when no type filter is selected', function () {
    asSuperAdmin();

    $typeA = ServiceRequestType::factory()->create();
    $typeB = ServiceRequestType::factory()->create();

    $priorityA = ServiceRequestPriority::factory()->create([
        'type_id' => $typeA->getKey(),
    ]);

    $priorityB = ServiceRequestPriority::factory()->create([
        'type_id' => $typeB->getKey(),
    ]);

    $serviceRequestsTypeA = ServiceRequest::factory()
        ->count(2)
        ->create(['priority_id' => $priorityA->getKey()]);

    $serviceRequestsTypeB = ServiceRequest::factory()
        ->count(2)
        ->create(['priority_id' => $priorityB->getKey()]);

    livewire(ListServiceRequests::class)
        ->filterTable('type', ['types' => []])
        ->assertCanSeeTableRecords($serviceRequestsTypeA->merge($serviceRequestsTypeB));
});

it('builds type tree options with categories as disabled groups and types as selectable items', function () {
    ServiceRequestType::query()->delete();
    ServiceRequestTypeCategory::query()->delete();

    $categoryA = ServiceRequestTypeCategory::factory()->create([
        'name' => 'Area A',
        'sort' => 1,
        'parent_id' => null,
    ]);

    $categoryB = ServiceRequestTypeCategory::factory()->create([
        'name' => 'Area B',
        'sort' => 2,
        'parent_id' => null,
    ]);

    $typeA1 = ServiceRequestType::factory()->create([
        'name' => 'Type A1',
        'sort' => 1,
        'category_id' => $categoryA->getKey(),
    ]);

    $typeA2 = ServiceRequestType::factory()->create([
        'name' => 'Type A2',
        'sort' => 2,
        'category_id' => $categoryA->getKey(),
    ]);

    $typeB1 = ServiceRequestType::factory()->create([
        'name' => 'Type B1',
        'sort' => 1,
        'category_id' => $categoryB->getKey(),
    ]);

    $tree = ListServiceRequests::buildTypeTreeOptions();

    expect($tree)->toHaveCount(2);

    // Area A
    expect($tree[0]['name'])->toBe('Area A');
    expect($tree[0]['disabled'])->toBeTrue();
    expect($tree[0]['value'])->toBe('category_' . $categoryA->getKey());
    expect($tree[0]['children'])->toHaveCount(2);
    expect($tree[0]['children'][0]['name'])->toBe('Type A1');
    expect($tree[0]['children'][0]['value'])->toBe($typeA1->getKey());
    expect($tree[0]['children'][0]['disabled'])->toBeFalse();
    expect($tree[0]['children'][1]['name'])->toBe('Type A2');
    expect($tree[0]['children'][1]['value'])->toBe($typeA2->getKey());
    expect($tree[0]['children'][1]['disabled'])->toBeFalse();

    // Area B
    expect($tree[1]['name'])->toBe('Area B');
    expect($tree[1]['disabled'])->toBeTrue();
    expect($tree[1]['value'])->toBe('category_' . $categoryB->getKey());
    expect($tree[1]['children'])->toHaveCount(1);
    expect($tree[1]['children'][0]['name'])->toBe('Type B1');
    expect($tree[1]['children'][0]['value'])->toBe($typeB1->getKey());
    expect($tree[1]['children'][0]['disabled'])->toBeFalse();
});

it('builds type tree options with nested categories maintaining sort order', function () {
    ServiceRequestType::query()->delete();
    ServiceRequestTypeCategory::query()->delete();

    $parentCategory = ServiceRequestTypeCategory::factory()->create([
        'name' => 'Parent Area',
        'sort' => 1,
        'parent_id' => null,
    ]);

    $childCategory = ServiceRequestTypeCategory::factory()->create([
        'name' => 'Child Area',
        'sort' => 1,
        'parent_id' => $parentCategory->getKey(),
    ]);

    $parentType = ServiceRequestType::factory()->create([
        'name' => 'Parent Type',
        'sort' => 2,
        'category_id' => $parentCategory->getKey(),
    ]);

    $childType = ServiceRequestType::factory()->create([
        'name' => 'Child Type',
        'sort' => 1,
        'category_id' => $childCategory->getKey(),
    ]);

    $tree = ListServiceRequests::buildTypeTreeOptions();

    expect($tree)->toHaveCount(1);

    // Parent Area
    expect($tree[0]['name'])->toBe('Parent Area');
    expect($tree[0]['disabled'])->toBeTrue();
    expect($tree[0]['children'])->toHaveCount(2);

    // Child Area (nested category, comes first by sort order)
    expect($tree[0]['children'][0]['name'])->toBe('Child Area');
    expect($tree[0]['children'][0]['disabled'])->toBeTrue();
    expect($tree[0]['children'][0]['children'])->toHaveCount(1);
    expect($tree[0]['children'][0]['children'][0]['name'])->toBe('Child Type');
    expect($tree[0]['children'][0]['children'][0]['value'])->toBe($childType->getKey());
    expect($tree[0]['children'][0]['children'][0]['disabled'])->toBeFalse();

    // Parent Type (type in parent category)
    expect($tree[0]['children'][1]['name'])->toBe('Parent Type');
    expect($tree[0]['children'][1]['value'])->toBe($parentType->getKey());
    expect($tree[0]['children'][1]['disabled'])->toBeFalse();
});

it('builds type tree options with uncategorized types at root level', function () {
    ServiceRequestType::query()->delete();
    ServiceRequestTypeCategory::query()->delete();

    $category = ServiceRequestTypeCategory::factory()->create([
        'name' => 'Categorized Area',
        'sort' => 1,
        'parent_id' => null,
    ]);

    ServiceRequestType::factory()->create([
        'name' => 'Categorized Type',
        'sort' => 1,
        'category_id' => $category->getKey(),
    ]);

    $uncategorizedType = ServiceRequestType::factory()->create([
        'name' => 'Uncategorized Type',
        'sort' => 1,
        'category_id' => null,
    ]);

    $tree = ListServiceRequests::buildTypeTreeOptions();

    expect($tree)->toHaveCount(2);

    // Uncategorized type at root level first
    expect($tree[0]['name'])->toBe('Uncategorized Type');
    expect($tree[0]['value'])->toBe($uncategorizedType->getKey());
    expect($tree[0]['disabled'])->toBeFalse();

    // Categorized area after uncategorized types
    expect($tree[1]['name'])->toBe('Categorized Area');
    expect($tree[1]['disabled'])->toBeTrue();
    expect($tree[1]['children'])->toHaveCount(1);
    expect($tree[1]['children'][0]['name'])->toBe('Categorized Type');
});
