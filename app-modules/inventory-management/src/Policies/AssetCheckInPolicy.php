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

namespace AidingApp\InventoryManagement\Policies;

use AidingApp\Contact\Models\Contact;
use AidingApp\InventoryManagement\Models\AssetCheckIn;
use App\Enums\Feature;
use App\Models\Authenticatable;
use App\Support\FeatureAccessResponse;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class AssetCheckInPolicy
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
            abilities: 'asset_check_in.view-any',
            denyResponse: 'You do not have permission to view asset check ins.'
        );
    }

    public function view(Authenticatable $authenticatable, AssetCheckIn $assetCheckIn): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['asset_check_in.*.view', "asset_check_in.{$assetCheckIn->id}.view"],
            denyResponse: 'You do not have permission to view this asset check in.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'asset_check_in.create',
            denyResponse: 'You do not have permission to create asset check ins.'
        );
    }

    public function update(Authenticatable $authenticatable, AssetCheckIn $assetCheckIn): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['asset_check_in.*.update', "asset_check_in.{$assetCheckIn->id}.update"],
            denyResponse: 'You do not have permission to update this asset check in.'
        );
    }

    public function delete(Authenticatable $authenticatable, AssetCheckIn $assetCheckIn): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['asset_check_in.*.delete', "asset_check_in.{$assetCheckIn->id}.delete"],
            denyResponse: 'You do not have permission to delete this asset check in.'
        );
    }

    public function restore(Authenticatable $authenticatable, AssetCheckIn $assetCheckIn): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['asset_check_in.*.restore', "asset_check_in.{$assetCheckIn->id}.restore"],
            denyResponse: 'You do not have permission to restore this asset check in.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, AssetCheckIn $assetCheckIn): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['asset_check_in.*.force-delete', "asset_check_in.{$assetCheckIn->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this asset check in.'
        );
    }

    /**
     * @return array<int, Feature>
     */
    protected function requiredFeatures(): array
    {
        return [Feature::ServiceManagement];
    }
}
