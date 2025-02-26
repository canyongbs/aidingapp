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
use AidingApp\ServiceManagement\Enums\SystemIncidentStatusClassification;
use AidingApp\ServiceManagement\Models\IncidentUpdate;
use App\Enums\Feature;
use App\Models\Authenticatable;
use App\Support\FeatureAccessResponse;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class IncidentUpdatePolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! $authenticatable->hasAnyLicense([Contact::getLicenseType()])) {
            return Response::deny('You are not licsensed for the Recruitment CRM.');
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
            abilities: 'incident_update.view-any',
            denyResponse: 'You do not have permissions to view incident updates.'
        );
    }

    public function view(Authenticatable $authenticatable, IncidentUpdate $incidentUpdate): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'incident_update.*.view',
            denyResponse: 'You do not have permissions to view this incident update.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'incident_update.create',
            denyResponse: 'You do not have permissions to create incident updates.'
        );
    }

    public function update(Authenticatable $authenticatable, IncidentUpdate $incidentUpdate): Response
    {
        if ($incidentUpdate->incident?->status?->classification === SystemIncidentStatusClassification::Resolved) {
            return Response::deny('Resolved incidents cannot be edited.');
        }

        return $authenticatable->canOrElse(
            abilities: 'incident_update.*.update',
            denyResponse: 'You do not have permissions to update this incident update.'
        );
    }

    public function delete(Authenticatable $authenticatable, IncidentUpdate $incidentUpdate): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'incident_update.*.delete',
            denyResponse: 'You do not have permissions to delete this incident update.'
        );
    }

    public function restore(Authenticatable $authenticatable, IncidentUpdate $incidentUpdate): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'incident_update.*.restore',
            denyResponse: 'You do not have permissions to restore this incident update.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, IncidentUpdate $incidentUpdate): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'incident_update.*.force-delete',
            denyResponse: 'You do not have permissions to force delete this incident update.'
        );
    }

    protected function requiredFeatures(): array
    {
        return [Feature::ServiceManagement];
    }
}
