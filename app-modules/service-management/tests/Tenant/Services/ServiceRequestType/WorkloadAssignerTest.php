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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeAssignmentTypes;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Services\ServiceRequestType\WorkloadAssigner;
use AidingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\travelBack;
use function Pest\Laravel\travelTo;
use function Tests\asSuperAdmin;

test('workload assigner assigns to user with fewest active requests', function () {
    asSuperAdmin();

    $team = Team::factory()
        ->has(User::factory()->count(3), 'users')
        ->create();

    $serviceRequestType = ServiceRequestType::factory()
        ->hasAttached($team, relationship: 'managerTeams')
        ->state([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Workload,
        ])
        ->create();

    $users = $team->users()->orderBy('name')->orderBy('id')->get();

    // Give the first two users 2 active assignments each
    foreach ($users->take(2) as $user) {
        for ($i = 0; $i < 2; $i++) {
            $existingRequest = ServiceRequest::factory()->state([
                'status_id' => ServiceRequestStatus::factory()->create([
                    'classification' => SystemServiceRequestClassification::Open,
                ])->getKey(),
                'priority_id' => ServiceRequestPriority::factory()->create([
                    'type_id' => $serviceRequestType->getKey(),
                ])->getKey(),
            ])->create();

            $existingRequest->assignments()->create([
                'user_id' => $user->getKey(),
                'assigned_at' => now(),
                'status' => ServiceRequestAssignmentStatus::Active,
            ]);
        }
    }

    // The third user has 0 assignments, should be assigned
    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    app(WorkloadAssigner::class)->execute($serviceRequest);

    $thirdUser = $users->get(2);

    expect($serviceRequest->assignedTo?->user_id)->toBe($thirdUser->getKey());
    expect($serviceRequestType->refresh()->last_assigned_id)->toBe($thirdUser->getKey());
});

test('workload assigner distributes evenly when all managers have same workload', function () {
    asSuperAdmin();

    $team = Team::factory()
        ->has(User::factory()->count(3), 'users')
        ->create();

    $serviceRequestType = ServiceRequestType::factory()
        ->hasAttached($team, relationship: 'managerTeams')
        ->state([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Workload,
        ])
        ->create();

    $users = $team->users()->orderBy('name')->orderBy('id')->get();

    travelTo(now()->subSeconds(count($users)));

    // All start with 0 workload — should assign in order
    foreach ($users as $user) {
        $serviceRequest = ServiceRequest::factory()->state([
            'status_id' => ServiceRequestStatus::factory()->create([
                'classification' => SystemServiceRequestClassification::Open,
            ])->getKey(),
            'priority_id' => ServiceRequestPriority::factory()->create([
                'type_id' => $serviceRequestType->getKey(),
            ])->getKey(),
        ])->create();

        app(WorkloadAssigner::class)->execute($serviceRequest);

        expect($serviceRequest->assignedTo?->user_id)->toBe($user->getKey());

        travelTo(now()->addSecond());
    }

    travelBack();
});

test('workload assigner does not assign when no managers exist', function () {
    asSuperAdmin();

    $serviceRequestType = ServiceRequestType::factory()->state([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::Workload,
    ])->create();

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    app(WorkloadAssigner::class)->execute($serviceRequest);

    expect($serviceRequest->assignments()->count())->toBe(0);
});
