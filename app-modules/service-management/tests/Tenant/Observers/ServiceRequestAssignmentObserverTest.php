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

use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailPreference;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestAssignedNotification;
use AidingApp\ServiceManagement\Notifications\ServiceRequestAssigned;
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

describe('Customer', function () {
    it('sends customer assigned notification when preference is enabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);

        $serviceRequest->assignments()->create([
            'user_id' => $manager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertSentTo($serviceRequest->respondent, SendEducatableServiceRequestAssignedNotification::class);
    });

    it('sends customer assigned notification with template when template exists', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Assigned,
            'role' => ServiceRequestTypeEmailTemplateRole::Customer,
        ]);

        $serviceRequest->assignments()->create([
            'user_id' => $manager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertSentTo($serviceRequest->respondent, SendEducatableServiceRequestAssignedNotification::class);
    });

    it('sends customer assigned notification without template when no template exists', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);

        $serviceRequest->assignments()->create([
            'user_id' => $manager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertSentTo($serviceRequest->respondent, SendEducatableServiceRequestAssignedNotification::class);
    });

    it('does not send customer assigned notification when preference is disabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email, false);

        $serviceRequest->assignments()->create([
            'user_id' => $manager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertNotSentTo($serviceRequest->respondent, SendEducatableServiceRequestAssignedNotification::class);
    });
});

describe('Manager', function () {
    it('sends manager assigned email with template when template exists', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Assigned,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Assigned,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        $serviceRequest->assignments()->create([
            'user_id' => $assignedManager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertSentTo($otherManager, ServiceRequestAssigned::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends manager assigned email without template when no template exists', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        $serviceRequest->assignments()->create([
            'user_id' => $assignedManager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertSentTo($otherManager, ServiceRequestAssigned::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send manager assigned email when preference is disabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email, false);

        $serviceRequest->assignments()->create([
            'user_id' => $assignedManager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertNotSentTo($otherManager, ServiceRequestAssigned::class);
    });

    it('sends manager assigned database notification when preference is enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification);

        $serviceRequest->assignments()->create([
            'user_id' => $assignedManager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertSentTo($otherManager, ServiceRequestAssigned::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send manager assigned database notification when preference is disabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification, false);

        $serviceRequest->assignments()->create([
            'user_id' => $assignedManager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertNotSentTo($otherManager, ServiceRequestAssigned::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });
});

describe('Auditor', function () {
    it('sends auditor assigned email with template when template exists', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);
        $type->auditorUsers()->attach($auditor);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Assigned,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Assigned,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Update,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);

        $serviceRequest->assignments()->create([
            'user_id' => $manager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertSentTo($auditor, ServiceRequestAssigned::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends auditor assigned email without template when no template exists', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);
        $type->auditorUsers()->attach($auditor);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email);

        $serviceRequest->assignments()->create([
            'user_id' => $manager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertSentTo($auditor, ServiceRequestAssigned::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send auditor assigned email when preference is disabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);
        $type->auditorUsers()->attach($auditor);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email, false);

        $serviceRequest->assignments()->create([
            'user_id' => $manager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertNotSentTo($auditor, ServiceRequestAssigned::class);
    });

    it('sends auditor assigned database notification when preference is enabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);
        $type->auditorUsers()->attach($auditor);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification);

        $serviceRequest->assignments()->create([
            'user_id' => $manager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertSentTo($auditor, ServiceRequestAssigned::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send auditor assigned database notification when preference is disabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);
        $type->auditorUsers()->attach($auditor);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification, false);

        $serviceRequest->assignments()->create([
            'user_id' => $manager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertNotSentTo($auditor, ServiceRequestAssigned::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });
});

describe('AssignedManager', function () {
    it('sends assigned manager assigned email with template when template exists', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Assigned,
            'role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Assigned,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        $serviceRequest->assignments()->create([
            'user_id' => $manager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertSentTo($manager, ServiceRequestAssigned::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends assigned manager assigned email without template when no template exists', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);

        $serviceRequest->assignments()->create([
            'user_id' => $manager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertSentTo($manager, ServiceRequestAssigned::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send assigned manager assigned email when preference is disabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email, false);

        $serviceRequest->assignments()->create([
            'user_id' => $manager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertNotSentTo($manager, ServiceRequestAssigned::class, function ($notification) {
            return $notification->channel === MailChannel::class;
        });
    });

    it('sends assigned manager assigned database notification when preference is enabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification);

        $serviceRequest->assignments()->create([
            'user_id' => $manager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertSentTo($manager, ServiceRequestAssigned::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send assigned manager assigned database notification when preference is disabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification, false);

        $serviceRequest->assignments()->create([
            'user_id' => $manager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        Notification::assertNotSentTo($manager, ServiceRequestAssigned::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });
});

describe('Deduplication', function () {
    it('assigned manager only receives one assigned email when both manager and assigned manager email preferences are enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        $managerTemplate = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Assigned,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        $assignedManagerTemplate = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Assigned,
            'role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ]);

        $serviceRequest->assignments()->create([
            'user_id' => $assignedManager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        $assignedManagerEmails = Notification::sent($assignedManager, ServiceRequestAssigned::class)
            ->filter(fn ($notification) => $notification->channel === MailChannel::class);

        expect($assignedManagerEmails)->toHaveCount(1);
        expect($assignedManagerEmails->first()->emailTemplate?->is($assignedManagerTemplate))->toBeTrue();

        Notification::assertSentTo($otherManager, ServiceRequestAssigned::class, function ($notification) use ($managerTemplate) {
            return $notification->channel === MailChannel::class && $notification->emailTemplate?->is($managerTemplate);
        });
    });

    it('assigned manager only receives one assigned database notification when both manager and assigned manager notification preferences are enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification);
        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification);

        $serviceRequest->assignments()->create([
            'user_id' => $assignedManager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        $assignedManagerDbNotifications = Notification::sent($assignedManager, ServiceRequestAssigned::class)
            ->filter(fn ($notification) => $notification->channel === DatabaseChannel::class);

        expect($assignedManagerDbNotifications)->toHaveCount(1);
    });

    it('assigned manager receives the manager broadcast when assigned manager preference is disabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        enablePreference($type, ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email, false);

        $serviceRequest->assignments()->create([
            'user_id' => $assignedManager->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        $assignedManagerEmails = Notification::sent($assignedManager, ServiceRequestAssigned::class)
            ->filter(fn ($notification) => $notification->channel === MailChannel::class);

        expect($assignedManagerEmails)->toHaveCount(1);
    });
});
