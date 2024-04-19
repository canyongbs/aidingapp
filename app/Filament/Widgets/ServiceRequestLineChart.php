<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use AidingApp\ServiceManagement\Models\ServiceRequest;

class ServiceRequestLineChart extends ChartWidget
{
    protected static ?string $heading = 'Service Requests in last 30 days';

    protected static ?string $maxHeight = '300px';

    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 3,
        'lg' => 3,
    ];

    protected function getData(): array
    {
        $totalCreatedPerDay = ServiceRequest::query()
            ->toBase()
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as total')
            ->where('created_at', '>', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $runningTotal = 0;
        $runningTotalPerDay = [];

        foreach (range(29, 0) as $daysAgo) {
            $date = Carbon::now()->subDays($daysAgo)->toDateString();

            $runningTotal += $totalCreatedPerDay[$date] ?? 0;

            $runningTotalPerDay[$date] = $runningTotal;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Service requests',
                    'data' => array_values($runningTotalPerDay),
                ],
            ],
            'labels' => array_keys($runningTotalPerDay),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
