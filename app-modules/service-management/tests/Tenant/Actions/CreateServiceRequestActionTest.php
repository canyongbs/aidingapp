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

        Notification::assertSentTo($serviceRequest->respondent, SendEducatableServiceRequestOpenedNotification::class);
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
    it('sends manager created notification when preference is enabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email);

        createServiceRequestViaAction($type);

        Notification::assertSentTo($manager, ServiceRequestCreated::class);
    });

    it('does not send manager created notification when preference is disabled', function () {
        Notification::fake();

        $manager = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->managerUsers()->attach($manager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email, false);

        createServiceRequestViaAction($type);

        Notification::assertNotSentTo($manager, ServiceRequestCreated::class);
    });
});

describe('Auditor', function () {
    it('sends auditor created notification when preference is enabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email);

        createServiceRequestViaAction($type);

        Notification::assertSentTo($auditor, ServiceRequestCreated::class);
    });

    it('does not send auditor created notification when preference is disabled', function () {
        Notification::fake();

        $auditor = User::factory()->create();
        $type = ServiceRequestType::factory()->create();
        $type->auditorUsers()->attach($auditor);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email, false);

        createServiceRequestViaAction($type);

        Notification::assertNotSentTo($auditor, ServiceRequestCreated::class);
    });
});

describe('AssignedManager', function () {
    it('sends assigned manager created notification when preference is enabled', function () {
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
            'role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ]);

        createServiceRequestViaAction($type);

        Notification::assertSentTo($assignedManager, ServiceRequestCreated::class);
    });

    it('does not send assigned manager created notification when preference is disabled', function () {
        Notification::fake();

        $assignedManager = User::factory()->create();
        $type = ServiceRequestType::factory()->create([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
            'assignment_type_individual_id' => $assignedManager->getKey(),
        ]);
        $type->managerUsers()->attach($assignedManager);

        enablePreference($type, ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email, false);

        createServiceRequestViaAction($type);

        Notification::assertNotSentTo($assignedManager, ServiceRequestCreated::class);
    });
});

describe('Deduplication', function () {
    it('assigned manager only receives one created notification when both manager and assigned manager preferences are enabled', function () {
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
        ServiceRequestTypeEmailTemplate::factory()->for($type, 'serviceRequestType')->create([
            'type' => ServiceRequestEmailTemplateType::Created,
            'role' => ServiceRequestTypeEmailTemplateRole::AssignedManager,
        ]);

        createServiceRequestViaAction($type);

        Notification::assertSentToTimes($assignedManager, ServiceRequestCreated::class, 1);
        Notification::assertSentToTimes($nonAssignedManager, ServiceRequestCreated::class, 1);
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

        createServiceRequestViaAction($type);

        Notification::assertSentToTimes($assignedManager, ServiceRequestCreated::class, 1);
    });
});
