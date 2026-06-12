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

namespace AidingApp\ServiceManagement\Observers;

use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\ServiceManagement\Actions\NotifyServiceRequestUsers;
use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Exceptions\AttemptedToAssignNonManagerToServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Notifications\Concerns\FetchServiceRequestTemplate;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestAssignedNotification;
use AidingApp\ServiceManagement\Notifications\ServiceRequestAssigned;
use AidingApp\Timeline\Events\TimelineableRecordCreated;
use AidingApp\Timeline\Events\TimelineableRecordDeleted;

class ServiceRequestAssignmentObserver
{
    use FetchServiceRequestTemplate;

    public function creating(ServiceRequestAssignment $serviceRequestAssignment): void
    {
        $type = $serviceRequestAssignment->serviceRequest->priority->type;

        $user = auth()->user();

        $isManager = $type->managerUsers()->where('users.id', $serviceRequestAssignment->user_id)->exists() ||
            $type->managerDepartments()->whereRelation('users', 'users.id', $serviceRequestAssignment->user_id)->exists();

        throw_if(! $isManager, new AttemptedToAssignNonManagerToServiceRequest());

        if (blank($serviceRequestAssignment->service_request_status_id)) {
            $serviceRequestAssignment->service_request_status_id = $serviceRequestAssignment->serviceRequest->status_id;
        }
    }

    public function created(ServiceRequestAssignment $serviceRequestAssignment): void
    {
        $serviceRequestAssignment->serviceRequest->assignments()->where('id', '!=', $serviceRequestAssignment->id)->update([
            'status' => ServiceRequestAssignmentStatus::Inactive,
        ]);

        TimelineableRecordCreated::dispatch($serviceRequestAssignment->serviceRequest, $serviceRequestAssignment);

        $customerEmailTemplate = $this->fetchTemplate(
            $serviceRequestAssignment->serviceRequest->priority->type,
            ServiceRequestEmailTemplateType::Assigned,
            ServiceRequestTypeEmailTemplateRole::Customer
        );

        if ($serviceRequestAssignment->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email)) {
            $serviceRequestAssignment->serviceRequest->respondent->notify(
                new SendEducatableServiceRequestAssignedNotification($serviceRequestAssignment->serviceRequest, $customerEmailTemplate)
            );
        }

        $managerEmailTemplate = $this->fetchTemplate(
            $serviceRequestAssignment->serviceRequest->priority->type,
            ServiceRequestEmailTemplateType::Assigned,
            ServiceRequestTypeEmailTemplateRole::Manager
        );

        $auditorEmailTemplate = $this->fetchTemplate(
            $serviceRequestAssignment->serviceRequest->priority->type,
            ServiceRequestEmailTemplateType::Assigned,
            ServiceRequestTypeEmailTemplateRole::Auditor
        );

        $shouldExcludeAssignedUserFromEmail = $serviceRequestAssignment->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email) ?? false;
        $shouldExcludeAssignedUserFromNotification = $serviceRequestAssignment->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification) ?? false;

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequestAssignment->serviceRequest,
            new ServiceRequestAssigned($serviceRequestAssignment->serviceRequest, $managerEmailTemplate, MailChannel::class),
            $serviceRequestAssignment->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email) ?? false,
            false,
            $shouldExcludeAssignedUserFromEmail ? $serviceRequestAssignment->user : null,
        );

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequestAssignment->serviceRequest,
            new ServiceRequestAssigned($serviceRequestAssignment->serviceRequest, $managerEmailTemplate, DatabaseChannel::class),
            $serviceRequestAssignment->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification) ?? false,
            false,
            $shouldExcludeAssignedUserFromNotification ? $serviceRequestAssignment->user : null,
        );

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequestAssignment->serviceRequest,
            new ServiceRequestAssigned($serviceRequestAssignment->serviceRequest, $auditorEmailTemplate, MailChannel::class),
            false,
            $serviceRequestAssignment->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email) ?? false,
        );

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequestAssignment->serviceRequest,
            new ServiceRequestAssigned($serviceRequestAssignment->serviceRequest, $auditorEmailTemplate, DatabaseChannel::class),
            false,
            $serviceRequestAssignment->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification) ?? false,
        );

        $assignedManagerEmailTemplate = $this->fetchTemplate(
            $serviceRequestAssignment->serviceRequest->priority->type,
            ServiceRequestEmailTemplateType::Assigned,
            ServiceRequestTypeEmailTemplateRole::AssignedManager
        );

        if ($serviceRequestAssignment->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email) ?? false) {
            $serviceRequestAssignment->user->notify(
                new ServiceRequestAssigned($serviceRequestAssignment->serviceRequest, $assignedManagerEmailTemplate, MailChannel::class)
            );
        }

        if ($serviceRequestAssignment->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Assigned, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification) ?? false) {
            $serviceRequestAssignment->user->notify(
                new ServiceRequestAssigned($serviceRequestAssignment->serviceRequest, $assignedManagerEmailTemplate, DatabaseChannel::class)
            );
        }
    }

    public function deleted(ServiceRequestAssignment $serviceRequestAssignment): void
    {
        TimelineableRecordDeleted::dispatch($serviceRequestAssignment->serviceRequest, $serviceRequestAssignment);
    }
}
