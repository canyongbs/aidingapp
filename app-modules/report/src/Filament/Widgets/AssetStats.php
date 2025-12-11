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

namespace AidingApp\Report\Filament\Widgets;

use AidingApp\InventoryManagement\Enums\MaintenanceActivityStatus;
use AidingApp\InventoryManagement\Models\Asset;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class AssetStats extends StatsOverviewReportWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 4,
        'lg' => 4,
    ];

    public function getStats(): array
    {
        return [
            Stat::make('Total Assets', Number::abbreviate(
                Cache::tags(["{{$this->cacheTag}}"])->remember('total-assets-count', now()->addHours(24), function (): int {
                    return Asset::count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('In Maintenance', Number::abbreviate(
                Cache::tags(["{{$this->cacheTag}}"])->remember('assets-in-maintenance-count', now()->addHours(24), function (): int {
                    return Asset::query()->whereHas('maintenanceActivities', function (Builder $query) {
                        $query->whereNotIn('status', [MaintenanceActivityStatus::Completed, MaintenanceActivityStatus::Canceled]);
                    })->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Checked Out', Number::abbreviate(
                Cache::tags(["{{$this->cacheTag}}"])->remember('assets-checked-out-count', now()->addHours(24), function (): int {
                    return Asset::query()->whereHas('checkOuts', function (Builder $query) {
                        $query->whereNull('asset_check_in_id');
                    })->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Bench Stock', Number::abbreviate(
                Cache::tags(["{{$this->cacheTag}}"])->remember('bench-stock-count', now()->addHours(24), function (): int {
                    return Asset::query()
                        ->whereDoesntHave('checkOuts', function (Builder $query) {
                            $query->whereNull('asset_check_in_id');
                        })
                        ->whereDoesntHave('maintenanceActivities', function (Builder $query) {
                            $query->whereNotIn('status', [MaintenanceActivityStatus::Completed, MaintenanceActivityStatus::Canceled]);
                        })
                        ->count();
                }),
                maxPrecision: 2,
            )),
        ];
    }
}
