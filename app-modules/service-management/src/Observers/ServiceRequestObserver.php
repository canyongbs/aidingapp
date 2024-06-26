<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\Notification\Events\TriggeredAutoSubscription;
use AidingApp\ServiceManagement\Actions\CreateServiceRequestHistory;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Exceptions\ServiceRequestNumberUpdateAttemptException;
use AidingApp\ServiceManagement\Notifications\SendClosedServiceFeedbackNotification;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestClosedNotification;
use AidingApp\ServiceManagement\Notifications\SendEducatableServiceRequestOpenedNotification;
use AidingApp\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;
use App\Enums\Feature;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ServiceRequestObserver
{
    public function creating(ServiceRequest $serviceRequest): void
    {
        $serviceRequest->service_request_number ??= app(ServiceRequestNumberGenerator::class)->generate();
    }

    public function created(ServiceRequest $serviceRequest): void
    {
        $user = auth()->user();

        if ($user instanceof User) {
            TriggeredAutoSubscription::dispatch($user, $serviceRequest);
        }

        if ($serviceRequest->status?->classification === SystemServiceRequestClassification::Open) {
            $serviceRequest->respondent->notify(new SendEducatableServiceRequestOpenedNotification($serviceRequest));
        }
    }

    public function updating(ServiceRequest $serviceRequest): void
    {
        throw_if($serviceRequest->isDirty('service_request_number'), new ServiceRequestNumberUpdateAttemptException());
    }

    public function saving(ServiceRequest $serviceRequest): void
    {
        if ($serviceRequest->wasChanged('status_id')) {
            $serviceRequest->status_updated_at = now();
        }
    }

    public function saved(ServiceRequest $serviceRequest): void
    {
        CreateServiceRequestHistory::dispatch($serviceRequest, $serviceRequest->getChanges(), $serviceRequest->getOriginal());

        if (
            $serviceRequest->wasChanged('status_id')
            && $serviceRequest->status?->classification === SystemServiceRequestClassification::Closed
        ) {
            $serviceRequest->respondent->notify(new SendEducatableServiceRequestClosedNotification($serviceRequest));
        }

        if (Gate::check(Feature::FeedbackManagement->getGateName()) && $serviceRequest?->priority?->type?->has_enabled_feedback_collection && $serviceRequest?->status?->classification == SystemServiceRequestClassification::Closed && !$serviceRequest?->feedback()->count()) {
          $serviceRequest->respondent->notify(new SendClosedServiceFeedbackNotification($serviceRequest));
      }
    }
}
