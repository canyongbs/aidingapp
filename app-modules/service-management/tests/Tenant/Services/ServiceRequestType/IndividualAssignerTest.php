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
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeAssignmentTypes;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailPreference;
use AidingApp\ServiceManagement\Notifications\ServiceRequestStatusChanged;
use AidingApp\ServiceManagement\Services\ServiceRequestType\IndividualAssigner;
use App\Features\AutomatedStatusChangeOnAssignmentFeature;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

use function Tests\asSuperAdmin;

if (! function_exists('enablePreference')) {
    function enablePreference(
        ServiceRequestType $type,
        ServiceRequestEmailTemplateType $templateType,
        ServiceRequestTypeEmailTemplateRole $role,
        ServiceRequestNotificationChannel $channel,
        bool $isEnabled = true,
    ): void {
        ServiceRequestTypeEmailPreference::create([
            'service_request_type_id' => $type->getKey(),
            'service_request_email_template_type' => $templateType,
            'service_request_email_template_role' => $role,
            'notification_channel' => $channel,
            'is_enabled' => $isEnabled,
        ]);
    }
}

/**
 * Builds a Service Request Type configured to auto-assign to $manager via the Individual assigner,
 * optionally with an automated status change target.
 */
function individualAutoAssignType(User $manager, ?ServiceRequestStatus $automatedStatus = null): ServiceRequestType
{
    return ServiceRequestType::factory()
        ->hasAttached($manager, relationship: 'managerUsers')
        ->state([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
            'assignment_type_individual_id' => $manager->getKey(),
            'automated_status_id' => $automatedStatus?->getKey(),
        ])
        ->create();
}

function serviceRequestForType(ServiceRequestType $type, ServiceRequestStatus $status): ServiceRequest
{
    return ServiceRequest::factory()->state([
        'status_id' => $status->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $type->getKey(),
        ])->getKey(),
    ])->create();
}

test('individual assigner assigns to configured user via department manager', function () {
    asSuperAdmin();

    $user = User::factory()->create();
    $department = Department::factory()->create();
    $user->department()->associate($department)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()
        ->hasAttached($department, relationship: 'managerDepartments')
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
    expect($assignment->assigned_by_type)->toBeNull();
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
    expect($assignment->assigned_by_id)->toBeNull();
    expect($assignment->assigned_by_type)->toBeNull();
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

describe('automated status change on assignment', function () {
    it('sets the request status when auto-assigning and the toggle is on', function () {
        asSuperAdmin();

        $manager = User::factory()->create();
        $target = ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::InProgress,
        ]);
        $type = individualAutoAssignType($manager, $target);
        $open = ServiceRequestStatus::factory()->open()->create();
        $serviceRequest = serviceRequestForType($type, $open);

        app(IndividualAssigner::class)->execute($serviceRequest);

        expect($serviceRequest->fresh()->status_id)->toBe($target->getKey());
    });

    it('does not change the status when no automated status is configured', function () {
        asSuperAdmin();

        $manager = User::factory()->create();
        $type = individualAutoAssignType($manager);
        $open = ServiceRequestStatus::factory()->open()->create();
        $serviceRequest = serviceRequestForType($type, $open);

        app(IndividualAssigner::class)->execute($serviceRequest);

        expect($serviceRequest->fresh()->status_id)->toBe($open->getKey());
    });

    it('leaves the status unchanged when the feature flag is inactive', function () {
        asSuperAdmin();

        AutomatedStatusChangeOnAssignmentFeature::deactivate();

        $manager = User::factory()->create();
        $target = ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::InProgress,
        ]);
        $type = individualAutoAssignType($manager, $target);
        $open = ServiceRequestStatus::factory()->open()->create();
        $serviceRequest = serviceRequestForType($type, $open);

        app(IndividualAssigner::class)->execute($serviceRequest);

        expect($serviceRequest->fresh()->status_id)->toBe($open->getKey());
    });

    it('sends status change notifications for the automated change (send as normal)', function () {
        Notification::fake();

        asSuperAdmin();

        $manager = User::factory()->create();
        $target = ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::InProgress,
        ]);
        $type = individualAutoAssignType($manager, $target);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification);
        $open = ServiceRequestStatus::factory()->open()->create();
        $serviceRequest = serviceRequestForType($type, $open);

        app(IndividualAssigner::class)->execute($serviceRequest);

        Notification::assertSentTo($manager, ServiceRequestStatusChanged::class);
    });

    it('does not send a duplicate notification when the request is already at the target status', function () {
        Notification::fake();

        asSuperAdmin();

        $manager = User::factory()->create();
        $target = ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::InProgress,
        ]);
        $type = individualAutoAssignType($manager, $target);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification);
        $serviceRequest = serviceRequestForType($type, $target);

        app(IndividualAssigner::class)->execute($serviceRequest);

        expect($serviceRequest->fresh()->status_id)->toBe($target->getKey());
        Notification::assertNotSentTo($manager, ServiceRequestStatusChanged::class);
    });
});
