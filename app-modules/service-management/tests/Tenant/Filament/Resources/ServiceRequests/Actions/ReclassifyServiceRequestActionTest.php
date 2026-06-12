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
use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeAssignmentTypes;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\ViewServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Services\ServiceRequestType\IndividualAssigner;
use AidingApp\ServiceManagement\Services\ServiceRequestType\RoundRobinAssigner;
use AidingApp\ServiceManagement\Services\ServiceRequestType\WorkloadAssigner;
use App\Features\ServiceRequestAssignmentByTypeFeature;
use App\Models\User;
use Filament\Notifications\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

// Authorization

test('reclassify action is visible for manager department member with update permission', function () {
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
        ->assertActionVisible('reclassify');
});

test('reclassify action is hidden for user without update permission', function () {
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
        ->assertActionHidden('reclassify');
});

test('reclassify action is hidden on closed service requests', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create();
    $user->department()->associate($department)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managerDepartments()->attach($department);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
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
        ->assertActionHidden('reclassify');
});

// Validation

test('reclassify requires type_id', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->callAction('reclassify', data: [
            'type_id' => null,
            'priority_id' => null,
            'assignment_method' => 'default',
        ])
        ->assertHasFormErrors(['type_id' => 'required']);
});

test('reclassify requires priority_id', function () {
    $originalType = ServiceRequestType::factory()->create();
    $newType = ServiceRequestType::factory()->create();

    $originalPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $originalType->getKey(),
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
        ->callAction('reclassify', data: [
            'type_id' => $newType->getKey(),
            'priority_id' => null,
            'assignment_method' => 'default',
        ])
        ->assertHasFormErrors(['priority_id' => 'required']);
});

test('reclassify requires assignment_method', function () {
    $originalType = ServiceRequestType::factory()->create();
    $newType = ServiceRequestType::factory()->create();

    $originalPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $originalType->getKey(),
    ]);

    $newPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $newType->getKey(),
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
        ->callAction('reclassify', data: [
            'type_id' => $newType->getKey(),
            'priority_id' => $newPriority->getKey(),
            'assignment_method' => null,
        ])
        ->assertHasFormErrors(['assignment_method' => 'required']);
});

test('reclassify with override but missing assign_to produces validation error', function () {
    $originalType = ServiceRequestType::factory()->create();
    $newType = ServiceRequestType::factory()->create();

    $originalPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $originalType->getKey(),
    ]);

    $newPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $newType->getKey(),
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
        ->callAction('reclassify', data: [
            'type_id' => $newType->getKey(),
            'priority_id' => $newPriority->getKey(),
            'assignment_method' => 'override',
            'assign_to' => null,
        ])
        ->assertHasFormErrors(['assign_to' => 'required']);
});

// Form Behavior / Options

test('type options exclude the current service request type', function () {
    $currentType = ServiceRequestType::factory()->create();
    $otherType = ServiceRequestType::factory()->create();

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $currentType->getKey(),
        ])->getKey(),
    ])->create();

    asSuperAdmin();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->mountAction('reclassify')
        ->assertFormFieldExists('type_id', function ($field) use ($currentType, $otherType): bool {
            $options = $field->getOptions();

            return ! isset($options[$currentType->getKey()])
                && isset($options[$otherType->getKey()]);
        });
});

test('type options exclude archived service request types', function () {
    $currentType = ServiceRequestType::factory()->create();
    $archivedType = ServiceRequestType::factory()->create([
        'archived_at' => now(),
    ]);
    $activeType = ServiceRequestType::factory()->create();

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $currentType->getKey(),
        ])->getKey(),
    ])->create();

    asSuperAdmin();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->mountAction('reclassify')
        ->assertFormFieldExists('type_id', function ($field) use ($archivedType, $activeType): bool {
            $options = $field->getOptions();

            return ! isset($options[$archivedType->getKey()])
                && isset($options[$activeType->getKey()]);
        });
});

test('override assign_to only lists eligible managers for selected type', function () {
    $originalType = ServiceRequestType::factory()->create();
    $newType = ServiceRequestType::factory()->create();

    $eligibleDirectManager = User::factory()->create(['name' => 'Eligible Direct']);
    $newType->managerUsers()->attach($eligibleDirectManager);

    $eligibleDepartmentManager = User::factory()->create(['name' => 'Eligible Department']);
    $department = Department::factory()->create();
    $eligibleDepartmentManager->department()->associate($department)->save();
    $newType->managerDepartments()->attach($department);

    $ineligibleUser = User::factory()->create(['name' => 'Ineligible User']);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $originalType->getKey(),
        ])->getKey(),
    ])->create();

    asSuperAdmin();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->mountAction('reclassify')
        ->fillForm([
            'type_id' => $newType->getKey(),
            'assignment_method' => 'override',
        ])
        ->assertFormFieldExists('assign_to', function ($field) use ($eligibleDirectManager, $eligibleDepartmentManager, $ineligibleUser): bool {
            $options = $field->getOptions();

            return isset($options[$eligibleDirectManager->getKey()])
                && isset($options[$eligibleDepartmentManager->getKey()])
                && ! isset($options[$ineligibleUser->getKey()]);
        });
});

test('selecting a new type auto-selects priority with matching name', function () {
    $originalType = ServiceRequestType::factory()->create();
    $newType = ServiceRequestType::factory()->create();

    $sharedName = 'High';

    $originalPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $originalType->getKey(),
        'name' => $sharedName,
    ]);

    $matchingPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $newType->getKey(),
        'name' => $sharedName,
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
        ->mountAction('reclassify')
        ->fillForm(['type_id' => $newType->getKey()])
        ->assertSchemaStateSet(['priority_id' => $matchingPriority->getKey()]);
});

test('selecting a new type clears priority when no matching name exists', function () {
    $originalType = ServiceRequestType::factory()->create();
    $newType = ServiceRequestType::factory()->create();

    $originalPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $originalType->getKey(),
        'name' => 'Urgent',
    ]);

    ServiceRequestPriority::factory()->create([
        'type_id' => $newType->getKey(),
        'name' => 'Low',
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
        ->mountAction('reclassify')
        ->fillForm(['type_id' => $newType->getKey()])
        ->assertSchemaStateSet(['priority_id' => null]);
});

// Happy Path

test('reclassify with default assignment updates priority_id', function () {
    $originalType = ServiceRequestType::factory()->create([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::None,
    ]);

    $newType = ServiceRequestType::factory()->create([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::None,
    ]);

    $originalPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $originalType->getKey(),
    ]);

    $newPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $newType->getKey(),
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
        ->callAction('reclassify', data: [
            'type_id' => $newType->getKey(),
            'priority_id' => $newPriority->getKey(),
            'assignment_method' => 'default',
        ])
        ->assertHasNoFormErrors();

    expect($serviceRequest->refresh()->priority_id)->toBe($newPriority->getKey());
});

test('reclassify with override assignment creates manual assignment to selected user', function () {
    ServiceRequestAssignmentByTypeFeature::activate();
    $originalType = ServiceRequestType::factory()->create([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::None,
    ]);

    $newType = ServiceRequestType::factory()->create([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::None,
    ]);

    $originalPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $originalType->getKey(),
    ]);

    $newPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $newType->getKey(),
    ]);

    $eligibleAgent = User::factory()->create();
    $newType->managerUsers()->attach($eligibleAgent);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => $originalPriority->getKey(),
    ])->create();

    $actor = User::factory()->create();
    asSuperAdmin($actor);

    actingAs($actor->refresh());

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->callAction('reclassify', data: [
            'type_id' => $newType->getKey(),
            'priority_id' => $newPriority->getKey(),
            'assignment_method' => 'override',
            'assign_to' => $eligibleAgent->getKey(),
        ])
        ->assertHasNoFormErrors();

    $serviceRequest->refresh();

    expect($serviceRequest->priority_id)->toBe($newPriority->getKey());

    $assignment = ServiceRequestAssignment::where('service_request_id', $serviceRequest->getKey())
        ->where('user_id', $eligibleAgent->getKey())
        ->where('assigned_by_id', $actor->getKey())
        ->where('assigned_by_type', (new User())->getMorphClass())
        ->where('status', ServiceRequestAssignmentStatus::Active)
        ->first();

    expect($assignment)->not->toBeNull();
    expect($assignment->user_id)->toBe($eligibleAgent->getKey());
    expect($assignment->assigned_by_id)->toBe($actor->getKey());
    expect($assignment->assigned_by_type)->toBe((new User())->getMorphClass());
});

test('reclassify deletes existing active assignment', function () {
    $originalType = ServiceRequestType::factory()->create([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::None,
    ]);

    $newType = ServiceRequestType::factory()->create([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::None,
    ]);

    $originalPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $originalType->getKey(),
    ]);

    $newPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $newType->getKey(),
    ]);

    $previousAssignee = User::factory()->create();
    $originalType->managerUsers()->attach($previousAssignee);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => $originalPriority->getKey(),
    ])->create();

    $serviceRequest->assignments()->create([
        'user_id' => $previousAssignee->getKey(),
        'assigned_by_id' => null,
        'assigned_by_type' => null,
        'assigned_at' => now(),
        'status' => ServiceRequestAssignmentStatus::Active,
    ]);

    expect($serviceRequest->assignments()->where('status', ServiceRequestAssignmentStatus::Active)->count())->toBe(1);

    asSuperAdmin();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->callAction('reclassify', data: [
            'type_id' => $newType->getKey(),
            'priority_id' => $newPriority->getKey(),
            'assignment_method' => 'default',
        ])
        ->assertHasNoFormErrors();

    expect($serviceRequest->refresh()->assignments()->where('status', ServiceRequestAssignmentStatus::Active)->count())->toBe(0);
    expect(ServiceRequestAssignment::withTrashed()->where('service_request_id', $serviceRequest->getKey())->where('user_id', $previousAssignee->getKey())->first()?->trashed())->toBeTrue();
});

test('reclassify with default assignment invokes the correct assigner class', function (ServiceRequestTypeAssignmentTypes $assignmentType, ?string $assignerClass) {
    $manager = User::factory()->create();

    $originalType = ServiceRequestType::factory()->create([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::None,
    ]);

    $newType = ServiceRequestType::factory()->create([
        'assignment_type' => $assignmentType,
        'assignment_type_individual_id' => $assignmentType === ServiceRequestTypeAssignmentTypes::Individual ? $manager->getKey() : null,
    ]);

    $newType->managerUsers()->attach($manager);

    $originalPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $originalType->getKey(),
    ]);

    $newPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $newType->getKey(),
    ]);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => $originalPriority->getKey(),
    ])->create();

    if ($assignerClass) {
        $spy = Mockery::spy($assignerClass);
        app()->instance($assignerClass, $spy);
    }

    asSuperAdmin();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->callAction('reclassify', data: [
            'type_id' => $newType->getKey(),
            'priority_id' => $newPriority->getKey(),
            'assignment_method' => 'default',
        ])
        ->assertHasNoFormErrors();

    if ($assignerClass) {
        $spy->shouldHaveReceived('execute')->once();
    } else {
        expect($serviceRequest->refresh()->assignments()->where('status', ServiceRequestAssignmentStatus::Active)->count())->toBe(0);
    }
})->with([
    'none' => [ServiceRequestTypeAssignmentTypes::None, null],
    'individual' => [ServiceRequestTypeAssignmentTypes::Individual, IndividualAssigner::class],
    'round robin' => [ServiceRequestTypeAssignmentTypes::RoundRobin, RoundRobinAssigner::class],
    'workload' => [ServiceRequestTypeAssignmentTypes::Workload, WorkloadAssigner::class],
]);

test('reclassify with individual assignment end-to-end assigns correct user', function () {
    $manager = User::factory()->create();

    $originalType = ServiceRequestType::factory()->create([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::None,
    ]);

    $newType = ServiceRequestType::factory()->create([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
        'assignment_type_individual_id' => $manager->getKey(),
    ]);

    $newType->managerUsers()->attach($manager);

    $originalPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $originalType->getKey(),
    ]);

    $newPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $newType->getKey(),
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
        ->callAction('reclassify', data: [
            'type_id' => $newType->getKey(),
            'priority_id' => $newPriority->getKey(),
            'assignment_method' => 'default',
        ])
        ->assertHasNoFormErrors();

    $serviceRequest->refresh();

    expect($serviceRequest->priority_id)->toBe($newPriority->getKey());
    expect($serviceRequest->assignedTo?->user_id)->toBe($manager->getKey());
});

// Error Handling

test('reclassify rolls back changes on failure and shows error notification', function () {
    $originalType = ServiceRequestType::factory()->create([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::None,
    ]);

    $newType = ServiceRequestType::factory()->create([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
        'assignment_type_individual_id' => null,
    ]);

    $originalPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $originalType->getKey(),
    ]);

    $newPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $newType->getKey(),
    ]);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => $originalPriority->getKey(),
    ])->create();

    $mock = Mockery::mock(IndividualAssigner::class);
    $mock->shouldReceive('execute')->andThrow(new RuntimeException('Assigner failed'));
    app()->instance(IndividualAssigner::class, $mock);

    asSuperAdmin();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->callAction('reclassify', data: [
            'type_id' => $newType->getKey(),
            'priority_id' => $newPriority->getKey(),
            'assignment_method' => 'default',
        ])
        ->assertNotified(
            Notification::make()
                ->title('Something went wrong reclassifying the service request.')
                ->danger()
        );

    expect($serviceRequest->refresh()->priority_id)->toBe($originalPriority->getKey());
});

test('reclassify shows success notification on completion', function () {
    $originalType = ServiceRequestType::factory()->create([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::None,
    ]);

    $newType = ServiceRequestType::factory()->create([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::None,
    ]);

    $originalPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $originalType->getKey(),
    ]);

    $newPriority = ServiceRequestPriority::factory()->create([
        'type_id' => $newType->getKey(),
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
        ->callAction('reclassify', data: [
            'type_id' => $newType->getKey(),
            'priority_id' => $newPriority->getKey(),
            'assignment_method' => 'default',
        ])
        ->assertNotified(
            Notification::make()
                ->title('Service request reclassified successfully.')
                ->success()
        );
});
