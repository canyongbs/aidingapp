<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Widgets;

use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ServiceUptimeWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 4;

    public ?ServiceMonitoringTarget $record = null;

    protected function getStats(): array
    {
        return [
            Stat::make('1 Day', .5),
            Stat::make('7 Days', '5%'),
            Stat::make('30 Days', '5%'),
            Stat::make('1 Year', '5%'),
        ];
    }
}
