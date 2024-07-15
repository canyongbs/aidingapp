<?php

namespace AidingApp\Report\Filament\Widgets;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;

class ServiceRequestPriorityDistributionDonutChart extends ChartReportWidget
{
    protected static ?string $heading = 'Request Priority Distribution';

    protected static ?string $maxHeight = '250px';

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 2,
        'lg' => 2,
    ];

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        return [];
        $serviceRequestByPriority = Cache::tags([$this->cacheTag])->remember('service-request-priority-distribution', now()->addHours(24), function (): Collection {
            $serviceRequestByPriorityData = ServiceRequestPriority::withCount(['serviceRequests'])->groupBy('type_id')->get(['id', 'name','type_id']);

            $serviceRequestByPriorityData = $serviceRequestByPriorityData->map(function (ServiceRequestPriority $status) {
                $status['bg_color'] = $this->getRgbString();

                return $status;
            });

            return $serviceRequestByPriorityData;
        });

        return [
            'labels' => $serviceRequestByPriority->pluck('name'),
            'datasets' => [
                [
                    'label' => 'My First Dataset',
                    'data' => $serviceRequestByPriority->pluck('service_requests_count'),
                    'backgroundColor' => $serviceRequestByPriority->pluck('bg_color'),
                    'hoverOffset' => 4,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getRgbString(): string
    {
        return 'rgb(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ')';
    }
}
