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

namespace AidingApp\ServiceManagement\Policies;

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Enums\Feature;
use App\Models\Authenticatable;
use App\Support\FeatureAccessResponse;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class ServiceRequestPolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! $authenticatable->hasAnyLicense([Contact::getLicenseType()])) {
            return Response::deny('You are not licensed for the Recruitment CRM.');
        }

        if (! Gate::check(
            collect($this->requiredFeatures())->map(fn (Feature $feature) => $feature->getGateName())
        )) {
            return FeatureAccessResponse::deny();
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'service_request.view-any',
            denyResponse: 'You do not have permission to view service requests.'
        );
    }

    public function view(Authenticatable $authenticatable, ServiceRequest $serviceRequest): Response
    {
        if (! $authenticatable->hasLicense($serviceRequest->respondent->getLicenseType())) {
            return Response::deny('You do not have permission to view this service request.');
        }

        if (! auth()->user()->isSuperAdmin()) {
            $team = auth()->user()->team;

            if (! $serviceRequest?->priority?->type?->managers?->contains('id', $team?->getKey()) && ! $serviceRequest?->priority?->type?->auditors?->contains('id', $team?->getKey())) {
                return Response::deny("You don't have permission to view this service request because you're not an auditor or manager.");
            }
        }

        return $authenticatable->canOrElse(
            abilities: ['service_request.*.view', "service_request.{$serviceRequest->id}.view"],
            denyResponse: 'You do not have permission to view this service request.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        if (! auth()->user()->isSuperAdmin()) {
            $team = auth()->user()->team;

            if (! $team?->manageableServiceRequestTypes()->exists()) {
                return Response::deny("You don't have permission to create service requests because you're not a manager of any service request types.");
            }
        }

        return $authenticatable->canOrElse(
            abilities: 'service_request.create',
            denyResponse: 'You do not have permission to create service requests.'
        );
    }

    public function update(Authenticatable $authenticatable, ServiceRequest $serviceRequest): Response
    {
        if (! $authenticatable->hasLicense($serviceRequest->respondent->getLicenseType())) {
            return Response::deny('You do not have permission to update this service request.');
        }

        if ($serviceRequest?->status?->classification === SystemServiceRequestClassification::Closed) {
            return Response::deny('Closed service request cannot be edited.');
        }

        if (! auth()->user()->isSuperAdmin()) {
            $team = auth()->user()->team;

            if (! $serviceRequest?->priority?->type?->managers?->contains('id', $team?->getKey())) {
                return Response::deny("You don't have permission to update this service request because you're not a manager of it's type.");
            }
        }

        return $authenticatable->canOrElse(
            abilities: ['service_request.*.update', "service_request.{$serviceRequest->id}.update"],
            denyResponse: 'You do not have permission to update this service request.'
        );
    }

    public function delete(Authenticatable $authenticatable, ServiceRequest $serviceRequest): Response
    {
        if (! $authenticatable->hasLicense($serviceRequest->respondent->getLicenseType())) {
            return Response::deny('You do not have permission to delete this service request.');
        }

        if (! auth()->user()->isSuperAdmin()) {
            $team = auth()->user()->team;

            if (! $serviceRequest?->priority?->type?->managers?->contains('id', $team?->getKey())) {
                return Response::deny("You don't have permission to delete this service request because you're not a manager of it's type.");
            }
        }

        return $authenticatable->canOrElse(
            abilities: ['service_request.*.delete', "service_request.{$serviceRequest->id}.delete"],
            denyResponse: 'You do not have permission to delete this service request.'
        );
    }

    public function restore(Authenticatable $authenticatable, ServiceRequest $serviceRequest): Response
    {
        if (! $authenticatable->hasLicense($serviceRequest->respondent->getLicenseType())) {
            return Response::deny('You do not have permission to restore this service request.');
        }

        if (! auth()->user()->isSuperAdmin()) {
            $team = auth()->user()->team;

            if (! $serviceRequest?->priority?->type?->managers?->contains('id', $team?->getKey())) {
                return Response::deny("You don't have permission to restore this service request because you're not a manager of it's type.");
            }
        }

        return $authenticatable->canOrElse(
            abilities: ['service_request.*.restore', "service_request.{$serviceRequest->id}.restore"],
            denyResponse: 'You do not have permission to restore this service request.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, ServiceRequest $serviceRequest): Response
    {
        if (! $authenticatable->hasLicense($serviceRequest->respondent->getLicenseType())) {
            return Response::deny('You do not have permission to permanently delete this service request.');
        }

        if (! auth()->user()->isSuperAdmin()) {
            $team = auth()->user()->team;

            if (! $serviceRequest?->priority?->type?->managers?->contains('id', $team?->getKey())) {
                return Response::deny("You don't have permission to permanently delete this service request because you're not a manager of it's type.");
            }
        }

        return $authenticatable->canOrElse(
            abilities: ['service_request.*.force-delete', "service_request.{$serviceRequest->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this service request.'
        );
    }

    protected function requiredFeatures(): array
    {
        return [Feature::ServiceManagement];
    }
}
