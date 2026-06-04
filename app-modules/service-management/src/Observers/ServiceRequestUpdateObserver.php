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
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AidingApp\ServiceManagement\Notifications\Concerns\FetchServiceRequestTemplate;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestUpdatedNotification;
use AidingApp\ServiceManagement\Notifications\ServiceRequestUpdated;
use AidingApp\Timeline\Events\TimelineableRecordCreated;
use AidingApp\Timeline\Events\TimelineableRecordDeleted;

class ServiceRequestUpdateObserver
{
    use FetchServiceRequestTemplate;

    public function creating(ServiceRequestUpdate $serviceRequestUpdate): void
    {
        if (
            // @phpstan-ignore function.impossibleType
            // @phpstan-ignore booleanAnd.alwaysFalse
            auth()->check()
            && is_null($serviceRequestUpdate->createdBy) // @phpstan-ignore function.impossibleType
        ) {
            $serviceRequestUpdate->createdBy()->associate(auth()->user());
        }
    }

    public function created(ServiceRequestUpdate $serviceRequestUpdate): void
    {
        TimelineableRecordCreated::dispatch($serviceRequestUpdate->serviceRequest, $serviceRequestUpdate);

        $customerEmailTemplate = $this->fetchTemplate(
            $serviceRequestUpdate->serviceRequest->priority->type,
            ServiceRequestEmailTemplateType::Update,
            ServiceRequestTypeEmailTemplateRole::Customer
        );

        if (
            ! $serviceRequestUpdate->internal
            && $serviceRequestUpdate->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email)
        ) {
            $serviceRequestUpdate->serviceRequest->respondent->notify(
                new SendEducatableServiceRequestUpdatedNotification($serviceRequestUpdate->serviceRequest, $customerEmailTemplate)
            );
        }

        $managerEmailTemplate = $this->fetchTemplate(
            $serviceRequestUpdate->serviceRequest->priority->type,
            ServiceRequestEmailTemplateType::Update,
            ServiceRequestTypeEmailTemplateRole::Manager
        );

        $auditorEmailTemplate = $this->fetchTemplate(
            $serviceRequestUpdate->serviceRequest->priority->type,
            ServiceRequestEmailTemplateType::Update,
            ServiceRequestTypeEmailTemplateRole::Auditor
        );

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequestUpdate->serviceRequest,
            new ServiceRequestUpdated($serviceRequestUpdate, $managerEmailTemplate, MailChannel::class),
            $serviceRequestUpdate->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email) ?? false,
            false,
        );

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequestUpdate->serviceRequest,
            new ServiceRequestUpdated($serviceRequestUpdate, $auditorEmailTemplate, MailChannel::class),
            false,
            $serviceRequestUpdate->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email) ?? false,
        );

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequestUpdate->serviceRequest,
            new ServiceRequestUpdated($serviceRequestUpdate, $managerEmailTemplate, DatabaseChannel::class),
            $serviceRequestUpdate->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification) ?? false,
            false,
        );

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequestUpdate->serviceRequest,
            new ServiceRequestUpdated($serviceRequestUpdate, $auditorEmailTemplate, DatabaseChannel::class),
            false,
            $serviceRequestUpdate->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification) ?? false,
        );

        $assignedManagerUpdateEmailTemplate = $this->fetchTemplate(
            $serviceRequestUpdate->serviceRequest->priority->type,
            ServiceRequestEmailTemplateType::Update,
            ServiceRequestTypeEmailTemplateRole::AssignedManager
        );

        if ($assignedUser = $serviceRequestUpdate->serviceRequest->assignedTo?->user) {
            if ($serviceRequestUpdate->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email) ?? false) {
                $assignedUser->notify(new ServiceRequestUpdated($serviceRequestUpdate, $assignedManagerUpdateEmailTemplate, MailChannel::class));
            }

            if ($serviceRequestUpdate->serviceRequest->priority?->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Update, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification) ?? false) {
                $assignedUser->notify(new ServiceRequestUpdated($serviceRequestUpdate, $assignedManagerUpdateEmailTemplate, DatabaseChannel::class));
            }
        }
    }

    public function deleted(ServiceRequestUpdate $serviceRequestUpdate): void
    {
        TimelineableRecordDeleted::dispatch($serviceRequestUpdate->serviceRequest, $serviceRequestUpdate);
    }
}
