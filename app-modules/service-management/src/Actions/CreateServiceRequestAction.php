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

namespace AidingApp\ServiceManagement\Actions;

use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\ServiceManagement\DataTransferObjects\ServiceRequestDataObject;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\ServiceManagement\Notifications\ServiceRequestCreated;
use Illuminate\Support\Facades\DB;

class CreateServiceRequestAction
{
    public function __construct(
        protected AssignServiceRequestToDepartment $assignServiceRequestToDepartment,
    ) {}

    public function execute(ServiceRequestDataObject $serviceRequestDataObject): ServiceRequest
    {
        return DB::transaction(function () use ($serviceRequestDataObject) {
            $serviceRequest = new ServiceRequest($serviceRequestDataObject->toArray());
            $serviceRequest->save();

            $this->assignServiceRequestToDepartment->execute($serviceRequest);

            $this->notifyManagersOfCreation($serviceRequest);

            return $serviceRequest;
        });
    }

    protected function notifyManagersOfCreation(ServiceRequest $serviceRequest): void
    {
        if (! $serviceRequest->priority) {
            return;
        }

        $type = $serviceRequest->priority->type;

        $assignedUser = $serviceRequest->assignedTo?->user;

        $shouldExcludeAssignedUserFromEmail = $assignedUser && $type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
        $shouldExcludeAssignedUserFromNotification = $assignedUser && $type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification);

        $managerEmailTemplate = ServiceRequestTypeEmailTemplate::query()
            ->where('service_request_type_id', $type->getKey())
            ->where('type', ServiceRequestEmailTemplateType::Created)
            ->where('role', ServiceRequestTypeEmailTemplateRole::Manager)
            ->first();

        $auditorEmailTemplate = ServiceRequestTypeEmailTemplate::query()
            ->where('service_request_type_id', $type->getKey())
            ->where('type', ServiceRequestEmailTemplateType::Created)
            ->where('role', ServiceRequestTypeEmailTemplateRole::Auditor)
            ->first();

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequest,
            new ServiceRequestCreated($serviceRequest, $managerEmailTemplate, MailChannel::class),
            $type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email),
            false,
            $shouldExcludeAssignedUserFromEmail ? $assignedUser : null,
        );

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequest,
            new ServiceRequestCreated($serviceRequest, $auditorEmailTemplate, MailChannel::class),
            false,
            $type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email),
        );

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequest,
            new ServiceRequestCreated($serviceRequest, $managerEmailTemplate, DatabaseChannel::class),
            $type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification),
            false,
            $shouldExcludeAssignedUserFromNotification ? $assignedUser : null,
        );

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequest,
            new ServiceRequestCreated($serviceRequest, $auditorEmailTemplate, DatabaseChannel::class),
            false,
            $type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification),
        );

        if ($assignedUser) {
            $assignedManagerCreatedEmailTemplate = ServiceRequestTypeEmailTemplate::query()
                ->where('service_request_type_id', $type->getKey())
                ->where('type', ServiceRequestEmailTemplateType::Created)
                ->where('role', ServiceRequestTypeEmailTemplateRole::AssignedManager)
                ->first();

            if ($type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email)) {
                $assignedUser->notify(new ServiceRequestCreated($serviceRequest, $assignedManagerCreatedEmailTemplate, MailChannel::class));
            }

            if ($type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification)) {
                $assignedUser->notify(new ServiceRequestCreated($serviceRequest, $assignedManagerCreatedEmailTemplate, DatabaseChannel::class));
            }
        }
    }
}
