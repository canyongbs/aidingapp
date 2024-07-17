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
        'md' => 2,
        'lg' => 2,
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
        $serviceRequestTotalPerMonth = Cache::tags([$this->cacheTag])->remember('service-requests-over-time-line-chart', now()->addHours(24), function (): array {
            $serviceRequestTotalPerMonthData = ServiceRequest::query()
                ->toBase()
                ->selectRaw('date_trunc(\'month\', created_at) as month')
                ->selectRaw('COUNT(*) as total')
                ->where('created_at', '>', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $serviceRequestTotalPerMonthArray = [];

            foreach (range(11, 0) as $month) {
                $month = Carbon::now()->subMonths($month);

                $serviceRequestTotalPerMonthArray[$month->format('M Y')] = $serviceRequestTotalPerMonthData[$month->startOfMonth()->toDateTimeString()] ?? 0;
            }

            return $serviceRequestTotalPerMonthArray;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Service requests',
                    'data' => array_values($serviceRequestTotalPerMonth),
                ],
            ],
            'labels' => array_keys($serviceRequestTotalPerMonth),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
