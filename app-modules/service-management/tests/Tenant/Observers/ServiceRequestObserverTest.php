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
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailPreference;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestClosedNotification;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestStatusChangeNotification;
use AidingApp\ServiceManagement\Notifications\ServiceRequestClosed;
use AidingApp\ServiceManagement\Notifications\ServiceRequestStatusChanged;
use App\Features\ServiceRequestTypeEmailPreferenceFeature;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    ServiceRequestTypeEmailPreferenceFeature::activate();
});

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

describe('Closed → Customer', function () {
    it('sends customer closed notification when preference is enabled and status becomes Closed', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $closedStatus = ServiceRequestStatus::factory()->closed()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertSentTo($serviceRequest->respondent, SendEducatableServiceRequestClosedNotification::class);
    });

    it('does not send customer closed notification when preference is disabled', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $closedStatus = ServiceRequestStatus::factory()->closed()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email, false);

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertNotSentTo($serviceRequest->respondent, SendEducatableServiceRequestClosedNotification::class);
    });
});

describe('Closed → Manager', function () {
    it('sends manager closed notification when preference is enabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $closedStatus = ServiceRequestStatus::factory()->closed()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertSentTo($manager, ServiceRequestClosed::class);
    });

    it('does not send manager closed notification when preference is disabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $closedStatus = ServiceRequestStatus::factory()->closed()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email, false);

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertNotSentTo($manager, ServiceRequestClosed::class);
    });
});

describe('Closed → Auditor', function () {
    it('sends auditor closed notification when preference is enabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $closedStatus = ServiceRequestStatus::factory()->closed()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertSentTo($auditor, ServiceRequestClosed::class);
    });

    it('does not send auditor closed notification when preference is disabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $closedStatus = ServiceRequestStatus::factory()->closed()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email, false);

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertNotSentTo($auditor, ServiceRequestClosed::class);
    });
});

describe('Closed → AssignedManager', function () {
    it('sends assigned manager closed notification when preference is enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ]);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $closedStatus = ServiceRequestStatus::factory()->closed()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->assignments()->create([
            'user_id' => $assignedManager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertSentTo($assignedManager, ServiceRequestClosed::class);
    });

    it('does not send assigned manager closed notification when preference is disabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $closedStatus = ServiceRequestStatus::factory()->closed()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->assignments()->create([
            'user_id' => $assignedManager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email, false);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email, false);

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertNotSentTo($assignedManager, ServiceRequestClosed::class);
    });
});

describe('Closed → Deduplication', function () {
    it('assigned manager only receives one closed notification when both manager and assigned manager preferences are enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ]);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $closedStatus = ServiceRequestStatus::factory()->closed()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->assignments()->create([
            'user_id' => $assignedManager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertSentToTimes($assignedManager, ServiceRequestClosed::class, 1);
        Notification::assertSentToTimes($otherManager, ServiceRequestClosed::class, 1);
    });
});

describe('StatusChange → Customer', function () {
    it('sends customer status change notification when preference is enabled', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $inProgressStatus = ServiceRequestStatus::factory()->inProgress()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertSentTo($serviceRequest->respondent, SendEducatableServiceRequestStatusChangeNotification::class);
    });

    it('does not send customer status change notification when preference is disabled', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $inProgressStatus = ServiceRequestStatus::factory()->inProgress()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email, false);

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertNotSentTo($serviceRequest->respondent, SendEducatableServiceRequestStatusChangeNotification::class);
    });
});

describe('StatusChange → Manager', function () {
    it('sends manager status change notification when preference is enabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $inProgressStatus = ServiceRequestStatus::factory()->inProgress()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertSentTo($manager, ServiceRequestStatusChanged::class);
    });

    it('does not send manager status change notification when preference is disabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $inProgressStatus = ServiceRequestStatus::factory()->inProgress()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email, false);

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertNotSentTo($manager, ServiceRequestStatusChanged::class);
    });
});

describe('StatusChange → Auditor', function () {
    it('sends auditor status change notification when preference is enabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $inProgressStatus = ServiceRequestStatus::factory()->inProgress()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertSentTo($auditor, ServiceRequestStatusChanged::class);
    });

    it('does not send auditor status change notification when preference is disabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $inProgressStatus = ServiceRequestStatus::factory()->inProgress()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email, false);

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertNotSentTo($auditor, ServiceRequestStatusChanged::class);
    });
});

describe('StatusChange → AssignedManager', function () {
    it('sends assigned manager status change notification when preference is enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::StatusChange,
            'role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ]);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $inProgressStatus = ServiceRequestStatus::factory()->inProgress()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->assignments()->create([
            'user_id' => $assignedManager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertSentTo($assignedManager, ServiceRequestStatusChanged::class);
    });

    it('does not send assigned manager status change notification when preference is disabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $inProgressStatus = ServiceRequestStatus::factory()->inProgress()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->assignments()->create([
            'user_id' => $assignedManager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email, false);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email, false);

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertNotSentTo($assignedManager, ServiceRequestStatusChanged::class);
    });
});

describe('StatusChange → Deduplication', function () {
    it('assigned manager only receives one status change notification when both manager and assigned manager preferences are enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::StatusChange,
            'role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ]);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $inProgressStatus = ServiceRequestStatus::factory()->inProgress()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->assignments()->create([
            'user_id' => $assignedManager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertSentToTimes($assignedManager, ServiceRequestStatusChanged::class, 1);
        Notification::assertSentToTimes($otherManager, ServiceRequestStatusChanged::class, 1);
    });
});
