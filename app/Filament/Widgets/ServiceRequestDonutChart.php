<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;

class ServiceRequestDonutChart extends ChartWidget
{
    protected static ?string $heading = 'Service Requests Status';

    protected static ?string $maxHeight = '300px';

    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 1,
        'lg' => 1,
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
        $serviceRequestByStatus = ServiceRequestStatus::withCount(['serviceRequests'])->get(['id', 'name']);

        $serviceRequestByStatus = $serviceRequestByStatus->map(function($item){
            $item['bg_color'] = \Arr::get($this->getColorForStatus(), $item->color->value); 
            return $item;
        });

        \Log::debug($serviceRequestByStatus);

        return [
            'labels' => $serviceRequestByStatus->pluck('name'),
            'datasets' => [
                [
                    'label' => 'My First Dataset',
                    'data' => $serviceRequestByStatus->pluck('service_requests_count'),
                    'backgroundColor' => $serviceRequestByStatus->pluck('bg_color'),
                    'hoverOffset' => 4,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getColorForStatus(): array
    {
        return [
            'primary' => 'rgb(254,195,33)',
            'success' => 'rgb(74,222,128)',
            'info' => 'rgb(96, 165, 250)',
            'warning' => 'rgb(251,191,36)',
            'danger' => 'rgb(248,113,113)',
            'gray' => 'rgb(161,161,170)',
        ];
    }
}
