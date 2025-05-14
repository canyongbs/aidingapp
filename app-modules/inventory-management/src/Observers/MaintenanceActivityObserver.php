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

namespace AidingApp\InventoryManagement\Observers;

use AidingApp\InventoryManagement\Actions\UpdateAssetStatusBasedOnMaintenanceActivity;
use AidingApp\InventoryManagement\Models\MaintenanceActivity;
use AidingApp\InventoryManagement\Models\MaintenanceProvider;
use AidingApp\Timeline\Events\TimelineableRecordCreated;
use AidingApp\Timeline\Events\TimelineableRecordDeleted;
use App\Features\AssetCount;

class MaintenanceActivityObserver
{
    public function creating(MaintenanceActivity $maintenanceActivity): void
    {
        if (is_null($maintenanceActivity->scheduled_date) && ! is_null($maintenanceActivity->completed_date)) {
            $maintenanceActivity->scheduled_date = $maintenanceActivity->completed_date;
        }
    }

    public function created(MaintenanceActivity $maintenanceActivity): void
    {
        if (AssetCount::active()) {
            $provider = MaintenanceProvider::find($maintenanceActivity->maintenance_provider_id);

            if ($provider) {
                $provider->increment('assets_count');
            }
        }

        TimelineableRecordCreated::dispatch($maintenanceActivity->asset, $maintenanceActivity);

        resolve(UpdateAssetStatusBasedOnMaintenanceActivity::class)->handle($maintenanceActivity);
    }

    public function updated(MaintenanceActivity $maintenanceActivity): void
    {
        if (AssetCount::active()) {
            $originalProviderId = $maintenanceActivity->getOriginal('maintenance_provider_id');
            $newProviderId = $maintenanceActivity->maintenance_provider_id;

            if ($originalProviderId !== $newProviderId) {
                $originalProvider = MaintenanceProvider::find($originalProviderId);
                $newProvider = MaintenanceProvider::find($newProviderId);

                if ($originalProvider) {
                    $originalProvider->decrement('assets_count');
                }

                if ($newProvider) {
                    $newProvider->increment('assets_count');
                }
            }
        }

        resolve(UpdateAssetStatusBasedOnMaintenanceActivity::class)->handle($maintenanceActivity);
    }

    public function deleted(MaintenanceActivity $maintenanceActivity): void
    {
        TimelineableRecordDeleted::dispatch($maintenanceActivity->asset, $maintenanceActivity);
    }
}
