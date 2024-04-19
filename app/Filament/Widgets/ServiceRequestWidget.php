<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ServiceRequestWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Open Service Requests', ServiceRequest::whereHas('status', function ($query) {
                return $query->where('name', 'New');
            })->count()),
            Stat::make('Unassigned Service Requests', ServiceRequest::doesntHave('assignments')->count()),
        ];
    }
}
