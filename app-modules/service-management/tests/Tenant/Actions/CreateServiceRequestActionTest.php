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
use AidingApp\Division\Models\Division;
use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\ServiceManagement\Actions\CreateServiceRequestAction;
use AidingApp\ServiceManagement\DataTransferObjects\ServiceRequestDataObject;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeAssignmentTypes;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailPreference;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestOpenedNotification;
use AidingApp\ServiceManagement\Notifications\ServiceRequestCreated;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

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

if (! function_exists('createServiceRequestViaAction')) {
    function createServiceRequestViaAction(ServiceRequestType $type, ?ServiceRequestStatus $status = null): ServiceRequest
    {
        $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
        $status ??= ServiceRequestStatus::factory()->open()->create();
        $respondent = Contact::factory()->create();
        $division = Division::factory()->create();

        return app(CreateServiceRequestAction::class)->execute(
            ServiceRequestDataObject::fromData([
                'type_id' => $type->getKey(),
                'priority_id' => $priority->getKey(),
                'status_id' => $status->getKey(),
                'division_id' => $division->getKey(),
                'respondent_id' => $respondent->getKey(),
                'title' => 'Test service request',
            ])
        );
    }
}

describe('Customer', function () {
    it('sends customer created notification when preference is enabled and status is Open', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);

        $serviceRequest = createServiceRequestViaAction($type);

        Notification::assertSentTo($serviceRequest->respondent, SendEducatableServiceRequestOpenedNotification::class, function ($notification) {
            return is_null($notification->emailTemplate);
        });
    });

    it('sends customer created notification with template when template exists', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Customer,
        ]);

        $serviceRequest = createServiceRequestViaAction($type);

        Notification::assertSentTo($serviceRequest->respondent, SendEducatableServiceRequestOpenedNotification::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template);
        });
    });

    it('sends customer created notification without template when no template exists', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);

        $serviceRequest = createServiceRequestViaAction($type);

        Notification::assertSentTo($serviceRequest->respondent, SendEducatableServiceRequestOpenedNotification::class, function ($notification) {
            return is_null($notification->emailTemplate);
        });
    });

    it('does not send customer created notification when preference is disabled', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email, false);

        createServiceRequestViaAction($type);

        Notification::assertNothingSentTo(Contact::first(), SendEducatableServiceRequestOpenedNotification::class);
    });

    it('does not send customer created notification when status is not Open', function () {
        Notification::fake();

        $type = ServiceRequestType::factory()->create();
        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email);

        $status = ServiceRequestStatus::factory()->inProgress()->create();
        createServiceRequestViaAction($type, $status);

        Notification::assertNothingSentTo(Contact::first(), SendEducatableServiceRequestOpenedNotification::class);
    });
});

describe('Manager', function () {
    it('sends manager created email with template when template exists', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        createServiceRequestViaAction($type);

        Notification::assertSentTo($manager, ServiceRequestCreated::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends manager created email without template when no template exists', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        createServiceRequestViaAction($type);

        Notification::assertSentTo($manager, ServiceRequestCreated::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send manager created email when preference is disabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email, false);

        createServiceRequestViaAction($type);

        Notification::assertNotSentTo($manager, ServiceRequestCreated::class);
    });

    it('sends manager created database notification when preference is enabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification);

        createServiceRequestViaAction($type);

        Notification::assertSentTo($manager, ServiceRequestCreated::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send manager created database notification when preference is disabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification, false);

        createServiceRequestViaAction($type);

        Notification::assertNotSentTo($manager, ServiceRequestCreated::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });
});

describe('Auditor', function () {
    it('sends auditor created email with template when template exists', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Closed,
            'role' => ServiceRequestTypeEmailTemplateRole::Auditor,
        ]);

        createServiceRequestViaAction($type);

        Notification::assertSentTo($auditor, ServiceRequestCreated::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends auditor created email without template when no template exists', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        createServiceRequestViaAction($type);

        Notification::assertSentTo($auditor, ServiceRequestCreated::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send auditor created email when preference is disabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email, false);

        createServiceRequestViaAction($type);

        Notification::assertNotSentTo($auditor, ServiceRequestCreated::class);
    });

    it('sends auditor created database notification when preference is enabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification);

        createServiceRequestViaAction($type);

        Notification::assertSentTo($auditor, ServiceRequestCreated::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send auditor created database notification when preference is disabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification, false);

        createServiceRequestViaAction($type);

        Notification::assertNotSentTo($auditor, ServiceRequestCreated::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });
});

describe('AssignedManager', function () {
    it('sends assigned manager created email with template when template exists', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
            'assignment_type_individual_id' => $assignedManager->getKey(),
        ]);
        $type->managerUsers()->attach($assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        $template = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ]);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        createServiceRequestViaAction($type);

        Notification::assertSentTo($assignedManager, ServiceRequestCreated::class, function ($notification) use ($template) {
            return $notification->emailTemplate?->is($template) && $notification->channel === MailChannel::class;
        });
    });

    it('sends assigned manager created email without template when no template exists', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
            'assignment_type_individual_id' => $assignedManager->getKey(),
        ]);
        $type->managerUsers()->attach($assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        createServiceRequestViaAction($type);

        Notification::assertSentTo($assignedManager, ServiceRequestCreated::class, function ($notification) {
            return is_null($notification->emailTemplate) && $notification->channel === MailChannel::class;
        });
    });

    it('does not send assigned manager created email when preference is disabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
            'assignment_type_individual_id' => $assignedManager->getKey(),
        ]);
        $type->managerUsers()->attach($assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email, false);

        createServiceRequestViaAction($type);

        Notification::assertNotSentTo($assignedManager, ServiceRequestCreated::class, function ($notification) {
            return $notification->channel === MailChannel::class;
        });
    });

    it('sends assigned manager created database notification when preference is enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
            'assignment_type_individual_id' => $assignedManager->getKey(),
        ]);
        $type->managerUsers()->attach($assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification);

        createServiceRequestViaAction($type);

        Notification::assertSentTo($assignedManager, ServiceRequestCreated::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });

    it('does not send assigned manager created database notification when preference is disabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
            'assignment_type_individual_id' => $assignedManager->getKey(),
        ]);
        $type->managerUsers()->attach($assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification, false);

        createServiceRequestViaAction($type);

        Notification::assertNotSentTo($assignedManager, ServiceRequestCreated::class, function ($notification) {
            return $notification->channel === DatabaseChannel::class;
        });
    });
});

describe('Deduplication', function () {
    it('assigned manager only receives one created email when both manager and assigned manager email preferences are enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $nonAssignedManager = User::factory()->create();

        $type = ServiceRequestType::factory()->create([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
            'assignment_type_individual_id' => $assignedManager->getKey(),
        ]);
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($nonAssignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        $managerTemplate = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);
        $assignedManagerTemplate = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ]);

        createServiceRequestViaAction($type);

        $assignedManagerEmails = Notification::sent($assignedManager, ServiceRequestCreated::class)
            ->filter(fn ($notification) => $notification->channel === MailChannel::class);

        expect($assignedManagerEmails)->toHaveCount(1);
        expect($assignedManagerEmails->first()->emailTemplate?->is($assignedManagerTemplate))->toBeTrue();

        Notification::assertSentTo($nonAssignedManager, ServiceRequestCreated::class, function ($notification) use ($managerTemplate) {
            return $notification->channel === MailChannel::class && $notification->emailTemplate?->is($managerTemplate);
        });
    });

    it('assigned manager only receives one created database notification when both manager and assigned manager notification preferences are enabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $nonAssignedManager = User::factory()->create();

        $type = ServiceRequestType::factory()->create([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
            'assignment_type_individual_id' => $assignedManager->getKey(),
        ]);
        $type->managerUsers()->attach($assignedManager);
        $type->managerUsers()->attach($nonAssignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification);
        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification);

        createServiceRequestViaAction($type);

        $assignedManagerNotifications = Notification::sent($assignedManager, ServiceRequestCreated::class)
            ->filter(fn ($notification) => $notification->channel === DatabaseChannel::class);

        expect($assignedManagerNotifications)->toHaveCount(1);
    });

    it('assigned manager receives the manager broadcast when assigned manager preference is disabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();

        $type = ServiceRequestType::factory()->create([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
            'assignment_type_individual_id' => $assignedManager->getKey(),
        ]);
        $type->managerUsers()->attach($assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);
        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email, false);
        $managerTemplate = ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::Manager,
        ]);

        createServiceRequestViaAction($type);

        $assignedManagerEmails = Notification::sent($assignedManager, ServiceRequestCreated::class)
            ->filter(fn ($notification) => $notification->channel === MailChannel::class);

        expect($assignedManagerEmails)->toHaveCount(1);
        expect($assignedManagerEmails->first()->emailTemplate?->is($managerTemplate))->toBeTrue();
    });
});
