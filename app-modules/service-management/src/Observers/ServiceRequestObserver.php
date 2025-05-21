<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\ServiceManagement\Observers;

use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\ServiceManagement\Actions\CreateServiceRequestHistory;
use AidingApp\ServiceManagement\Actions\NotifyServiceRequestUsers;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Exceptions\ServiceRequestNumberUpdateAttemptException;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Notifications\Concerns\FetchServiceRequestTemplate;
use AidingApp\ServiceManagement\Notifications\SendClosedServiceFeedbackNotification;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestClosedNotification;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestOpenedNotification;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestStatusChangeNotification;
use AidingApp\ServiceManagement\Notifications\ServiceRequestClosed;
use AidingApp\ServiceManagement\Notifications\ServiceRequestCreated;
use AidingApp\ServiceManagement\Notifications\ServiceRequestStatusChanged;
use AidingApp\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;
use App\Enums\Feature;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class ServiceRequestObserver
{
    use FetchServiceRequestTemplate;

    public function creating(ServiceRequest $serviceRequest): void
    {
        $serviceRequest->service_request_number ??= app(ServiceRequestNumberGenerator::class)->generate();
    }

    public function created(ServiceRequest $serviceRequest): void
    {
        $customerEmailTemplate = $this->fetchTemplate(
            $serviceRequest->priority->type,
            ServiceRequestEmailTemplateType::Created,
            ServiceRequestTypeEmailTemplateRole::Customer
        );

        if ($serviceRequest->status?->classification === SystemServiceRequestClassification::Open
            && $serviceRequest->priority?->type->is_customers_service_request_created_email_enabled) {
            $serviceRequest->respondent->notify(
                new SendEducatableServiceRequestOpenedNotification($serviceRequest, $customerEmailTemplate)
            );
        }

        $managerEmailTemplate = $this->fetchTemplate(
            $serviceRequest->priority->type,
            ServiceRequestEmailTemplateType::Created,
            ServiceRequestTypeEmailTemplateRole::Manager
        );

        $auditorEmailTemplate = $this->fetchTemplate(
            $serviceRequest->priority->type,
            ServiceRequestEmailTemplateType::Created,
            ServiceRequestTypeEmailTemplateRole::Auditor
        );

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequest,
            new ServiceRequestCreated($serviceRequest, $managerEmailTemplate, MailChannel::class),
            $serviceRequest->priority?->type->is_managers_service_request_created_email_enabled ?? false,
            false,
        );

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequest,
            new ServiceRequestCreated($serviceRequest, $auditorEmailTemplate, MailChannel::class),
            false,
            $serviceRequest->priority?->type->is_auditors_service_request_created_email_enabled ?? false,
        );

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequest,
            new ServiceRequestCreated($serviceRequest, $managerEmailTemplate, DatabaseChannel::class),
            $serviceRequest->priority?->type->is_managers_service_request_created_notification_enabled ?? false,
            false,
        );

        app(NotifyServiceRequestUsers::class)->execute(
            $serviceRequest,
            new ServiceRequestCreated($serviceRequest, $auditorEmailTemplate, DatabaseChannel::class),
            false,
            $serviceRequest->priority?->type->is_auditors_service_request_created_notification_enabled ?? false,
        );
    }

    public function saving(ServiceRequest $serviceRequest): void
    {
        if ($serviceRequest->isDirty('status_id')) {
            $serviceRequest->status_updated_at = now();

            if (
                $serviceRequest->status->classification === SystemServiceRequestClassification::Closed &&
                is_null($serviceRequest->time_to_resolution)
            ) {
                $createdTime = $serviceRequest->created_at;
                $currentTime = Carbon::now();

                // Calculate the difference in seconds
                $secondsDifference = $createdTime ? round($createdTime->diffInSeconds($currentTime)) : null;
                $serviceRequest->time_to_resolution = $secondsDifference;
            }
        }
    }

    public function saved(ServiceRequest $serviceRequest): void
    {
        CreateServiceRequestHistory::dispatch($serviceRequest, $serviceRequest->getChanges(), $serviceRequest->getOriginal());

        $customerEmailTemplate = $this->fetchTemplate(
            $serviceRequest->priority->type,
            ServiceRequestEmailTemplateType::Closed,
            ServiceRequestTypeEmailTemplateRole::Customer
        );

        if (
            $serviceRequest->wasChanged('status_id')
            && $serviceRequest->status?->classification === SystemServiceRequestClassification::Closed
            && $serviceRequest->priority?->type->is_customers_service_request_closed_email_enabled
        ) {
            $serviceRequest->respondent->notify(new SendEducatableServiceRequestClosedNotification($serviceRequest, $customerEmailTemplate));
        }

        if (
            Gate::check(Feature::FeedbackManagement->getGateName()) &&
            $serviceRequest?->priority?->type?->has_enabled_feedback_collection &&
            $serviceRequest?->status?->classification == SystemServiceRequestClassification::Closed &&
            ! $serviceRequest?->feedback()->count()
        ) {
            if ($serviceRequest->priority->type->is_customers_survey_response_email_enabled) {
                $customerEmailTemplateForSurveyResponse = $this->fetchTemplate(
                    $serviceRequest->priority->type,
                    ServiceRequestEmailTemplateType::SurveyResponse,
                    ServiceRequestTypeEmailTemplateRole::Customer
                );

                $serviceRequest->respondent->notify(new SendClosedServiceFeedbackNotification($serviceRequest, $customerEmailTemplateForSurveyResponse));
            } else {
                $serviceRequest->respondent->notify(new SendClosedServiceFeedbackNotification($serviceRequest, null));
            }
        }
    }

    public function updating(ServiceRequest $serviceRequest): void
    {
        throw_if($serviceRequest->isDirty('service_request_number'), new ServiceRequestNumberUpdateAttemptException());
    }

    public function updated(ServiceRequest $serviceRequest): void
    {
        if ($serviceRequest->wasChanged('status_id')) {
            if ($serviceRequest->status?->classification === SystemServiceRequestClassification::Closed) {
                $managerEmailTemplate = $this->fetchTemplate(
                    $serviceRequest->priority->type,
                    ServiceRequestEmailTemplateType::Closed,
                    ServiceRequestTypeEmailTemplateRole::Manager
                );

                $auditorEmailTemplate = $this->fetchTemplate(
                    $serviceRequest->priority->type,
                    ServiceRequestEmailTemplateType::Closed,
                    ServiceRequestTypeEmailTemplateRole::Auditor
                );

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestClosed($serviceRequest, $managerEmailTemplate, MailChannel::class),
                    $serviceRequest->priority?->type->is_managers_service_request_closed_email_enabled ?? false,
                    false,
                );

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestClosed($serviceRequest, $auditorEmailTemplate, MailChannel::class),
                    false,
                    $serviceRequest->priority?->type->is_auditors_service_request_closed_email_enabled ?? false,
                );

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestClosed($serviceRequest, $managerEmailTemplate, DatabaseChannel::class),
                    $serviceRequest->priority?->type->is_managers_service_request_closed_notification_enabled ?? false,
                    false,
                );

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestClosed($serviceRequest, $auditorEmailTemplate, DatabaseChannel::class),
                    false,
                    $serviceRequest->priority?->type->is_auditors_service_request_closed_notification_enabled ?? false,
                );
            } elseif ($serviceRequest->status) {
                $customerEmailTemplate = $this->fetchTemplate(
                    $serviceRequest->priority->type,
                    ServiceRequestEmailTemplateType::StatusChange,
                    ServiceRequestTypeEmailTemplateRole::Customer
                );

                if ($serviceRequest->priority?->type->is_customers_service_request_status_change_email_enabled) {
                    $serviceRequest->respondent->notify(new SendEducatableServiceRequestStatusChangeNotification($serviceRequest, $customerEmailTemplate));
                }

                $managerEmailTemplate = $this->fetchTemplate(
                    $serviceRequest->priority->type,
                    ServiceRequestEmailTemplateType::StatusChange,
                    ServiceRequestTypeEmailTemplateRole::Manager
                );

                $auditorEmailTemplate = $this->fetchTemplate(
                    $serviceRequest->priority->type,
                    ServiceRequestEmailTemplateType::StatusChange,
                    ServiceRequestTypeEmailTemplateRole::Auditor
                );

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestStatusChanged($serviceRequest, $managerEmailTemplate, MailChannel::class),
                    $serviceRequest->priority?->type->is_managers_service_request_status_change_email_enabled ?? false,
                    false,
                );

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestStatusChanged($serviceRequest, $auditorEmailTemplate, MailChannel::class),
                    false,
                    $serviceRequest->priority?->type->is_auditors_service_request_status_change_email_enabled ?? false,
                );

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestStatusChanged($serviceRequest, $managerEmailTemplate, DatabaseChannel::class),
                    $serviceRequest->priority?->type->is_managers_service_request_status_change_notification_enabled ?? false,
                    false,
                );

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestStatusChanged($serviceRequest, $auditorEmailTemplate, DatabaseChannel::class),
                    false,
                    $serviceRequest->priority?->type->is_auditors_service_request_status_change_notification_enabled ?? false,
                );
            }
        }
    }
}
