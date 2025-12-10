<?php

namespace AidingApp\Report\Filament\Widgets;

use AidingApp\InventoryManagement\Enums\MaintenanceActivityStatus;
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\InventoryManagement\Models\AssetCheckOut;
use AidingApp\InventoryManagement\Models\MaintenanceActivity;
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

    protected function getStats(): array
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
                        $query->whereIn('status', [MaintenanceActivityStatus::Scheduled, MaintenanceActivityStatus::InProgress]);
                    })->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Checked Out', Number::abbreviate(
                Cache::tags(["{{$this->cacheTag}}"])->remember('assets-checked-out-count', now()->addHours(24), function (): int {
                    return AssetCheckOut::query()->whereNull('asset_check_in_id')->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Bench Stock', Number::abbreviate(
                Cache::tags(["{{$this->cacheTag}}"])->remember('bench-stock-count', now()->addHours(24), function (): int {
                    return Asset::query()->whereDoesntHave('checkOuts', function (Builder $query) {
                        $query->whereNull('asset_check_in_id');
                    })->count();
                }),
                maxPrecision: 2,
            )),
        ];
    }
}
