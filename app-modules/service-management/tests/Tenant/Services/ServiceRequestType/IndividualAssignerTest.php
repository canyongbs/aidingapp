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

use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeAssignmentTypes;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Services\ServiceRequestType\IndividualAssigner;
use AidingApp\Team\Models\Team;
use App\Models\User;

use function Tests\asSuperAdmin;

test('individual assigner assigns to configured user via team manager', function () {
    asSuperAdmin();

    $user = User::factory()->create();
    $team = Team::factory()->create();
    $user->team()->associate($team)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()
        ->hasAttached($team, relationship: 'managerTeams')
        ->state([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
            'assignment_type_individual_id' => $user->getKey(),
        ])
        ->create();

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    app(IndividualAssigner::class)->execute($serviceRequest);

    $assignment = $serviceRequest->assignments()->where('status', ServiceRequestAssignmentStatus::Active)->first();

    expect($assignment)->not->toBeNull();
    expect($assignment->user_id)->toBe($user->getKey());
    expect($assignment->assigned_by_id)->toBeNull();
});

test('individual assigner assigns to configured user via direct manager', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()
        ->hasAttached($user, relationship: 'managerUsers')
        ->state([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
            'assignment_type_individual_id' => $user->getKey(),
        ])
        ->create();

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    app(IndividualAssigner::class)->execute($serviceRequest);

    $assignment = $serviceRequest->assignments()->where('status', ServiceRequestAssignmentStatus::Active)->first();

    expect($assignment)->not->toBeNull();
    expect($assignment->user_id)->toBe($user->getKey());
});

test('individual assigner does not assign when no individual is configured', function () {
    asSuperAdmin();

    $serviceRequestType = ServiceRequestType::factory()->state([
        'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
        'assignment_type_individual_id' => null,
    ])->create();

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    app(IndividualAssigner::class)->execute($serviceRequest);

    expect($serviceRequest->assignments()->count())->toBe(0);
});
