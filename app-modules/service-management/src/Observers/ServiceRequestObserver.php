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
use AidingApp\ServiceManagement\Actions\CreateServiceRequestHistory;
use AidingApp\ServiceManagement\Actions\NotifyServiceRequestUsers;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
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
use AidingApp\ServiceManagement\Notifications\ServiceRequestStatusChanged;
use AidingApp\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;
use App\Enums\Feature;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class ServiceRequestObserver
{
    use FetchServiceRequestTemplate;

    public function creating(ServiceRequest $serviceRequest): void
    {
        $serviceRequest->service_request_number ??= app(ServiceRequestNumberGenerator::class)->generate();

        /** @phpstan-ignore function.impossibleType (Because this is in an observer it is possible that category is null before the model is persisted) */
        if (is_null($serviceRequest->category)) {
            $serviceRequest->category = $serviceRequest->priority->type->default_category;
        }
    }

    public function created(ServiceRequest $serviceRequest): void
    {
        if (! $serviceRequest->isDraft()) {
            $this->writeCreatedHistory($serviceRequest);
        }

        if (! $serviceRequest->priority) {
            return;
        }

        $customerEmailTemplate = $this->fetchTemplate(
            $serviceRequest->priority->type,
            ServiceRequestEmailTemplateType::Created,
            ServiceRequestTypeEmailTemplateRole::Customer
        );

        if ($serviceRequest->status?->classification === SystemServiceRequestClassification::Open
            && $serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Created, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email)) {
            $serviceRequest->respondent->notify(
                new SendEducatableServiceRequestOpenedNotification($serviceRequest, $customerEmailTemplate)
            );
        }

    }

    public function saving(ServiceRequest $serviceRequest): void
    {
        if ($serviceRequest->isDirty('status_id')) {
            $serviceRequest->status_updated_at = CarbonImmutable::now();

            if (
                $serviceRequest->status->classification === SystemServiceRequestClassification::Closed &&
                is_null($serviceRequest->time_to_resolution)
            ) {
                $createdTime = $serviceRequest->created_at;
                $currentTime = Carbon::now();

                // Calculate the difference in seconds
                $secondsDifference = $createdTime ? (int) round($createdTime->diffInSeconds($currentTime)) : null;
                $serviceRequest->time_to_resolution = $secondsDifference;
            }
        }
    }

    public function saved(ServiceRequest $serviceRequest): void
    {
        if (! $serviceRequest->isDraft() && ! $serviceRequest->wasRecentlyCreated) {
            $actor = $this->resolveActor();

            CreateServiceRequestHistory::dispatch(
                $serviceRequest,
                $serviceRequest->getChanges(),
                $serviceRequest->getOriginal(),
                $actor?->getMorphClass(),
                $actor?->getKey(),
            );
        }

        if (! $serviceRequest->priority) {
            return;
        }

        $customerEmailTemplate = $this->fetchTemplate(
            $serviceRequest->priority->type,
            ServiceRequestEmailTemplateType::Closed,
            ServiceRequestTypeEmailTemplateRole::Customer
        );

        if (
            $serviceRequest->wasChanged('status_id')
            && $serviceRequest->status?->classification === SystemServiceRequestClassification::Closed
            && $serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email)
        ) {
            $serviceRequest->respondent->notify(new SendEducatableServiceRequestClosedNotification($serviceRequest, $customerEmailTemplate));
        }

        if (
            Gate::check(Feature::FeedbackManagement->getGateName()) &&
            $serviceRequest->priority->type->has_enabled_feedback_collection &&
            $serviceRequest->status?->classification == SystemServiceRequestClassification::Closed &&
            ! $serviceRequest->feedback()->count()
        ) {
            if ($serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::SurveyResponse, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email)) {
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
        if (! $serviceRequest->priority) {
            return;
        }

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

                $assignedUser = $serviceRequest->assignedTo?->user;

                $shouldExcludeAssignedUserFromEmail = $assignedUser && $serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
                $shouldExcludeAssignedUserFromNotification = $assignedUser && $serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification);

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestClosed($serviceRequest, $managerEmailTemplate, MailChannel::class),
                    $serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email),
                    false,
                    $shouldExcludeAssignedUserFromEmail ? $assignedUser : null,
                );

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestClosed($serviceRequest, $auditorEmailTemplate, MailChannel::class),
                    false,
                    $serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email),
                );

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestClosed($serviceRequest, $managerEmailTemplate, DatabaseChannel::class),
                    $serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification),
                    false,
                    $shouldExcludeAssignedUserFromNotification ? $assignedUser : null,
                );

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestClosed($serviceRequest, $auditorEmailTemplate, DatabaseChannel::class),
                    false,
                    $serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification),
                );

                $assignedManagerClosedEmailTemplate = $this->fetchTemplate(
                    $serviceRequest->priority->type,
                    ServiceRequestEmailTemplateType::Closed,
                    ServiceRequestTypeEmailTemplateRole::AssignedManager
                );

                if ($assignedUser) {
                    if ($serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email)) {
                        $assignedUser->notify(new ServiceRequestClosed($serviceRequest, $assignedManagerClosedEmailTemplate, MailChannel::class));
                    }

                    if ($serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::Closed, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification)) {
                        $assignedUser->notify(new ServiceRequestClosed($serviceRequest, $assignedManagerClosedEmailTemplate, DatabaseChannel::class));
                    }
                }
            } elseif ($serviceRequest->status) {
                $customerEmailTemplate = $this->fetchTemplate(
                    $serviceRequest->priority->type,
                    ServiceRequestEmailTemplateType::StatusChange,
                    ServiceRequestTypeEmailTemplateRole::Customer
                );

                if ($serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Customer, ServiceRequestNotificationChannel::Email)) {
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

                $assignedUser = $serviceRequest->assignedTo?->user;

                $shouldExcludeAssignedUserFromEmail = $assignedUser && $serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email);
                $shouldExcludeAssignedUserFromNotification = $assignedUser && $serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification);

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestStatusChanged($serviceRequest, $managerEmailTemplate, MailChannel::class),
                    $serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Email),
                    false,
                    $shouldExcludeAssignedUserFromEmail ? $assignedUser : null,
                );

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestStatusChanged($serviceRequest, $auditorEmailTemplate, MailChannel::class),
                    false,
                    $serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Email),
                );

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestStatusChanged($serviceRequest, $managerEmailTemplate, DatabaseChannel::class),
                    $serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Manager, ServiceRequestNotificationChannel::Notification),
                    false,
                    $shouldExcludeAssignedUserFromNotification ? $assignedUser : null,
                );

                app(NotifyServiceRequestUsers::class)->execute(
                    $serviceRequest,
                    new ServiceRequestStatusChanged($serviceRequest, $auditorEmailTemplate, DatabaseChannel::class),
                    false,
                    $serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::Auditor, ServiceRequestNotificationChannel::Notification),
                );

                $assignedManagerStatusChangeEmailTemplate = $this->fetchTemplate(
                    $serviceRequest->priority->type,
                    ServiceRequestEmailTemplateType::StatusChange,
                    ServiceRequestTypeEmailTemplateRole::AssignedManager
                );

                if ($assignedUser) {
                    if ($serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Email)) {
                        $assignedUser->notify(new ServiceRequestStatusChanged($serviceRequest, $assignedManagerStatusChangeEmailTemplate, MailChannel::class));
                    }

                    if ($serviceRequest->priority->type->isPreferenceEnabled(ServiceRequestEmailTemplateType::StatusChange, ServiceRequestTypeEmailTemplateRole::AssignedManager, ServiceRequestNotificationChannel::Notification)) {
                        $assignedUser->notify(new ServiceRequestStatusChanged($serviceRequest, $assignedManagerStatusChangeEmailTemplate, DatabaseChannel::class));
                    }
                }
            }
        }
    }

    private function resolveActor(): ?Model
    {
        return auth()->user();
    }

    private function writeCreatedHistory(ServiceRequest $serviceRequest): void
    {
        $row = [
            // Empty `original_values` is the discriminator for the Creation event — see
            // ServiceRequestHistory::isCreatedEvent(). Cast to object so json_encode writes `{}`
            // (consistent shape with field-change rows that use JSON objects).
            'original_values' => (object) [],
            'new_values' => [
                'status_id' => $serviceRequest->status_id,
                'priority_id' => $serviceRequest->priority_id,
                'type_id' => $serviceRequest->priority?->type_id,
                'title' => $serviceRequest->title,
            ],
        ];

        $actor = $this->resolveActor();
        $row['actor_type'] = $actor?->getMorphClass();
        $row['actor_id'] = $actor?->getKey();

        $serviceRequest->histories()->create($row);
    }
}
