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
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestUpdatedNotification;
use AidingApp\ServiceManagement\Notifications\ServiceRequestUpdated;
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

if (! function_exists('createServiceRequestWithAssignment')) {
    function createServiceRequestWithAssignment(ServiceRequestType $type, User $assignedUser): ServiceRequest
    {
        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $status = ServiceRequestStatus::factory()->open()->create();
        $respondent = Contact::factory()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($priority, 'priority')
            ->for($status, 'status')
            ->create(['respondent_id' => $respondent->getKey()]);

        $serviceRequest->assignments()->create([
            'user_id' => $assignedUser->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        return $serviceRequest->refresh();
    }
}

describe('Customer', function () {
    it('sends customer update notification when preference is enabled and update is not internal', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertSentTo($serviceRequest->respondent, SendEducatableServiceRequestUpdatedNotification::class, function ($notification) {
            return is_null($notification->emailTemplate);
        });
    });

    it('sends customer update notification with template when template exists', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Update,
            'role' => ServiceRequestTypeEmailTemplateRole::Customer,
        ]);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertSentTo($serviceRequest->respondent, SendEducatableServiceRequestUpdatedNotification::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template);
        });
    });

    it('sends customer update notification without template when no template exists', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertSentTo($serviceRequest->respondent, SendEducatableServiceRequestUpdatedNotification::class, function ($notification) {
            return is_null($notification->emailTemplate);
        });
    });

    it('does not send customer update notification when preference is disabled', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email, false);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertNotSentTo($serviceRequest->respondent, SendEducatableServiceRequestUpdatedNotification::class);
    });

    it('does not send customer update notification when update is internal', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => true]);

        Notification::assertNotSentTo($serviceRequest->respondent, SendEducatableServiceRequestUpdatedNotification::class);
    });
});

describe('Manager', function () {
    it('sends manager update email with template when template exists', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Update,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Update,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertSentTo($manager, ServiceRequestUpdated::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends manager update email without template when no template exists', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertSentTo($manager, ServiceRequestUpdated::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send manager update email when preference is disabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email, false);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertNotSentTo($manager, ServiceRequestUpdated::class);
    });

    it('sends manager update database notification when preference is enabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertSentTo($manager, ServiceRequestUpdated::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send manager update database notification when preference is disabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification, false);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertNotSentTo($manager, ServiceRequestUpdated::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('sends manager update email for internal updates when preference is enabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => true]);

        Notification::assertSentTo($manager, ServiceRequestUpdated::class, function ($notification) {
            return $notification->channel === MailChannel::class;
        });
    });
});

describe('Auditor', function () {
    it('sends auditor update email with template when template exists', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Update,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Update,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertSentTo($auditor, ServiceRequestUpdated::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends auditor update email without template when no template exists', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertSentTo($auditor, ServiceRequestUpdated::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send auditor update email when preference is disabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email, false);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertNotSentTo($auditor, ServiceRequestUpdated::class);
    });

    it('sends auditor update database notification when preference is enabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertSentTo($auditor, ServiceRequestUpdated::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send auditor update database notification when preference is disabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification, false);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertNotSentTo($auditor, ServiceRequestUpdated::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('sends auditor update email for internal updates when preference is enabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->create();

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => true]);

        Notification::assertSentTo($auditor, ServiceRequestUpdated::class, function ($notification) {
            return $notification->channel === MailChannel::class;
        });
    });
});

describe('AssignedManager', function () {
    it('sends assigned manager update email with template when template exists', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);

        $serviceRequest = createServiceRequestWithAssignment($type, $assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Update,
            'role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Update,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertSentTo($assignedManager, ServiceRequestUpdated::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends assigned manager update email without template when no template exists', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);

        $serviceRequest = createServiceRequestWithAssignment($type, $assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertSentTo($assignedManager, ServiceRequestUpdated::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send assigned manager update email when preference is disabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);

        $serviceRequest = createServiceRequestWithAssignment($type, $assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email, false);
        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email, false);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertNotSentTo($assignedManager, ServiceRequestUpdated::class, function ($notification) {
            return $notification->channel === MailChannel::class;
        });
    });

    it('sends assigned manager update database notification when preference is enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);

        $serviceRequest = createServiceRequestWithAssignment($type, $assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertSentTo($assignedManager, ServiceRequestUpdated::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send assigned manager update database notification when preference is disabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);

        $serviceRequest = createServiceRequestWithAssignment($type, $assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification, false);
        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification, false);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        Notification::assertNotSentTo($assignedManager, ServiceRequestUpdated::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });
});

describe('Deduplication', function () {
    it('assigned manager only receives one update email when both manager and assigned manager email preferences are enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);

        $serviceRequest = createServiceRequestWithAssignment($type, $assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        $managerTemplate = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Update,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        $assignedManagerTemplate = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Update,
            'role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ]);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        $assignedManagerEmails = Notification::sent($assignedManager, ServiceRequestUpdated::class)
            ->filter(fn ($notification) => $notification->channel === MailChannel::class);

        expect($assignedManagerEmails)->toHaveCount(1);
        expect($assignedManagerEmails->first()->emailTemplate?->is($assignedManagerTemplate))->toBeTrue();

        Notification::assertSentTo($otherManager, ServiceRequestUpdated::class, function ($notification) use ($managerTemplate) {
            return $notification->channel === MailChannel::class && $notification->emailTemplate?->is($managerTemplate);
        });
    });

    it('assigned manager only receives one update database notification when both manager and assigned manager notification preferences are enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $otherManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($otherManager);

        $serviceRequest = createServiceRequestWithAssignment($type, $assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification);
        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        $assignedManagerDbNotifications = Notification::sent($assignedManager, ServiceRequestUpdated::class)
            ->filter(fn ($notification) => $notification->channel === DatabaseChannel::class);

        expect($assignedManagerDbNotifications)->toHaveCount(1);
    });

    it('assigned manager receives the manager broadcast when assigned manager preference is disabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($assignedManager);

        $serviceRequest = createServiceRequestWithAssignment($type, $assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        enablePreference($type, ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email, false);
        $managerTemplate = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Update,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create(['internal' => false]);

        $assignedManagerEmails = Notification::sent($assignedManager, ServiceRequestUpdated::class)
            ->filter(fn ($notification) => $notification->channel === MailChannel::class);

        expect($assignedManagerEmails)->toHaveCount(1);
        expect($assignedManagerEmails->first()->emailTemplate?->is($managerTemplate))->toBeTrue();
    });
});
