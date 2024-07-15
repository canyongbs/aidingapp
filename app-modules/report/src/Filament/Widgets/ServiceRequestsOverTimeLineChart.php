<?php

namespace AidingApp\Report\Filament\Widgets;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use AidingApp\ServiceManagement\Models\ServiceRequest;

class ServiceRequestsOverTimeLineChart extends ChartReportWidget
{
    protected static ?string $heading = 'Requests Over Time';

    protected static ?string $maxHeight = '250px';

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 4,
        'lg' => 4,
    ];

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'min' => 0,
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $serviceRequestTotalPerDay = Cache::tags([$this->cacheTag])->remember('service-requests-over-time-line-chart', now()->addHours(24), function (): array {
            $serviceRequestTotalPerDayData = ServiceRequest::query()
                ->toBase()
                ->selectRaw('DATE(created_at) as date')
                ->selectRaw('COUNT(*) as total')
                ->where('created_at', '>', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date');

            $serviceRequestTotalPerDayArray = [];

            foreach (range(29, 0) as $daysAgo) {
                $date = Carbon::now()->subDays($daysAgo)->toDateString();

                $serviceRequestTotalPerDayArray[$date] = $serviceRequestTotalPerDayData[$date] ?? 0;
            }

            return $serviceRequestTotalPerDayArray;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Service requests',
                    'data' => array_values($serviceRequestTotalPerDay),
                ],
            ],
            'labels' => array_keys($serviceRequestTotalPerDay),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
