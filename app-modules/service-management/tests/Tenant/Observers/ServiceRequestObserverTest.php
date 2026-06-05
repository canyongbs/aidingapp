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

use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
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
    it('sends customer closed notification when preference is enabled and template exists', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Customer,
        ]);

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

    it('sends customer closed notification when preference is enabled and no template exists', function () {
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
    it('sends manager closed email with template when template exists', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::StatusChange,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $closedStatus = ServiceRequestStatus::factory()->closed()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertSentTo($manager, ServiceRequestClosed::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends manager closed email without template when no template exists', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::StatusChange,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $closedStatus = ServiceRequestStatus::factory()->closed()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertSentTo($manager, ServiceRequestClosed::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send manager closed email when preference is disabled', function () {
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

    it('sends manager closed database notification when preference is enabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $closedStatus = ServiceRequestStatus::factory()->closed()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertSentTo($manager, ServiceRequestClosed::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send manager closed database notification when preference is disabled', function () {
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

        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification, false);

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertNotSentTo($manager, ServiceRequestClosed::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });
});

describe('Closed → Auditor', function () {
    it('sends auditor closed email with template when template exists', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::StatusChange,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $closedStatus = ServiceRequestStatus::factory()->closed()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertSentTo($auditor, ServiceRequestClosed::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends auditor closed email without template when no template exists', function () {
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

        Notification::assertSentTo($auditor, ServiceRequestClosed::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send auditor closed email when preference is disabled', function () {
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

    it('sends auditor closed database notification when preference is enabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $closedStatus = ServiceRequestStatus::factory()->closed()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertSentTo($auditor, ServiceRequestClosed::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send auditor closed database notification when preference is disabled', function () {
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

        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification, false);

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertNotSentTo($auditor, ServiceRequestClosed::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });
});

describe('Closed → AssignedManager', function () {
    it('sends assigned manager closed email with template when template exists', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
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

        Notification::assertSentTo($assignedManager, ServiceRequestClosed::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends assigned manager closed email without template when no template exists', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);

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

        Notification::assertSentTo($assignedManager, ServiceRequestClosed::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send assigned manager closed email when preference is disabled', function () {
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

    it('sends assigned manager closed database notification when preference is enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification);

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

        Notification::assertSentTo($assignedManager, ServiceRequestClosed::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send assigned manager closed database notification when preference is disabled', function () {
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

        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification, false);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification, false);

        $serviceRequest->status()->associate($closedStatus);
        $serviceRequest->save();

        Notification::assertNotSentTo($assignedManager, ServiceRequestClosed::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });
});

describe('Closed → Deduplication', function () {
    it('assigned manager only receives one closed email when both manager and assigned manager email preferences are enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        $managerTemplate = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        $assignedManagerTemplate = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
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

        $assignedManagerEmails = Notification::sent($assignedManager, ServiceRequestClosed::class)
            ->filter(fn ($notification) => $notification->channel === MailChannel::class);

        expect($assignedManagerEmails)->toHaveCount(1);
        expect($assignedManagerEmails->first()->emailTemplate?->is($assignedManagerTemplate))->toBeTrue();

        Notification::assertSentTo($otherManager, ServiceRequestClosed::class, function ($notification) use ($managerTemplate) {
            return $notification->channel === MailChannel::class && $notification->emailTemplate?->is($managerTemplate);
        });
    });

    it('assigned manager only receives one closed database notification when both manager and assigned manager notification preferences are enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification);

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

        $assignedManagerNotifications = Notification::sent($assignedManager, ServiceRequestClosed::class)
            ->filter(fn ($notification) => $notification->channel === DatabaseChannel::class);

        expect($assignedManagerNotifications)->toHaveCount(1);
    });

    it('assigned manager receives the manager broadcast when assigned manager preference is disabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        enablePreference($type, ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email, false);

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
    });
});

describe('StatusChange → Customer', function () {
    it('sends customer status change notification when preference is enabled and template exists', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::StatusChange,
            'role' => ServiceRequestTypeEmailTemplateRole::Customer,
        ]);

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

    it('sends customer status change notification when preference is enabled and no template exists', function () {
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
    it('sends manager status change email with template when template exists', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::StatusChange,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::StatusChange,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $inProgressStatus = ServiceRequestStatus::factory()->inProgress()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertSentTo($manager, ServiceRequestStatusChanged::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends manager status change email without template when no template exists', function () {
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

        Notification::assertSentTo($manager, ServiceRequestStatusChanged::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send manager status change email when preference is disabled', function () {
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

    it('sends manager status change database notification when preference is enabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $inProgressStatus = ServiceRequestStatus::factory()->inProgress()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertSentTo($manager, ServiceRequestStatusChanged::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send manager status change database notification when preference is disabled', function () {
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

        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification, false);

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertNotSentTo($manager, ServiceRequestStatusChanged::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });
});

describe('StatusChange → Auditor', function () {
    it('sends auditor status change email with template when template exists', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::StatusChange,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::StatusChange,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $inProgressStatus = ServiceRequestStatus::factory()->inProgress()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertSentTo($auditor, ServiceRequestStatusChanged::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends auditor status change email without template when no template exists', function () {
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

        Notification::assertSentTo($auditor, ServiceRequestStatusChanged::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send auditor status change email when preference is disabled', function () {
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

    it('sends auditor status change database notification when preference is enabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $openStatus = ServiceRequestStatus::factory()->open()->create();
        $inProgressStatus = ServiceRequestStatus::factory()->inProgress()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($openStatus, 'status')
            ->create();

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertSentTo($auditor, ServiceRequestStatusChanged::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send auditor status change database notification when preference is disabled', function () {
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

        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification, false);

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertNotSentTo($auditor, ServiceRequestStatusChanged::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });
});

describe('StatusChange → AssignedManager', function () {
    it('sends assigned manager status change email with template when template exists', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::StatusChange,
            'role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::StatusChange,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
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

        Notification::assertSentTo($assignedManager, ServiceRequestStatusChanged::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends assigned manager status change email without template when no template exists', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);

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

        Notification::assertSentTo($assignedManager, ServiceRequestStatusChanged::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send assigned manager status change email when preference is disabled', function () {
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

    it('sends assigned manager status change database notification when preference is enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification);

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

        Notification::assertSentTo($assignedManager, ServiceRequestStatusChanged::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send assigned manager status change database notification when preference is disabled', function () {
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

        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification, false);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification, false);

        $serviceRequest->status()->associate($inProgressStatus);
        $serviceRequest->save();

        Notification::assertNotSentTo($assignedManager, ServiceRequestStatusChanged::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });
});

describe('StatusChange → Deduplication', function () {
    it('assigned manager only receives one status change email when both manager and assigned manager email preferences are enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        $managerTemplate = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::StatusChange,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        $assignedManagerTemplate = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
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

        $assignedManagerEmails = Notification::sent($assignedManager, ServiceRequestStatusChanged::class)
            ->filter(fn ($notification) => $notification->channel === MailChannel::class);

        expect($assignedManagerEmails)->toHaveCount(1);
        expect($assignedManagerEmails->first()->emailTemplate?->is($assignedManagerTemplate))->toBeTrue();

        Notification::assertSentTo($otherManager, ServiceRequestStatusChanged::class, function ($notification) use ($managerTemplate) {
            return $notification->channel === MailChannel::class && $notification->emailTemplate?->is($managerTemplate);
        });
    });

    it('assigned manager only receives one status change database notification when both manager and assigned manager notification preferences are enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification);

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

        $assignedManagerNotifications = Notification::sent($assignedManager, ServiceRequestStatusChanged::class)
            ->filter(fn ($notification) => $notification->channel === DatabaseChannel::class);

        expect($assignedManagerNotifications)->toHaveCount(1);
    });

    it('assigned manager receives the manager broadcast when assigned manager preference is disabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        enablePreference($type, ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email, false);

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
    });
});
