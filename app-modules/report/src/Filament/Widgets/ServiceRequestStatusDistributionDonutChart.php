<?php

namespace AidingApp\Report\Filament\Widgets;

use Filament\Support\Colors\Color;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ServiceRequestStatusDistributionDonutChart extends ChartReportWidget
{
    protected static ?string $heading = 'Request Status Distribution';

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
        $serviceRequestByStatus = Cache::tags([$this->cacheTag])->remember('service-request-status-distribution', now()->addHours(24), function () : Collection {
            $serviceRequestByStatusData = ServiceRequestStatus::withCount(['serviceRequests'])->get(['id', 'name']);

            $serviceRequestByStatusData = $serviceRequestByStatusData->map(function (ServiceRequestStatus $status) {
                $status['bg_color'] = $this->getColorForStatus($status->color->value);

                return $status;
            });
            return $serviceRequestByStatusData;
        });

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

    protected function getColorForStatus($color)
    {
        return match ($color) {
            'primary' => $this->getRgbString(Color::Indigo[500]),
            'success' => $this->getRgbString(Color::Emerald[500]),
            'info' => $this->getRgbString(Color::Blue[500]),
            'warning' => $this->getRgbString(Color::Orange[500]),
            'danger' => $this->getRgbString(Color::Red[500]),
            'gray' => $this->getRgbString(Color::Gray[500]),
        };
    }

    protected function getRgbString($color): string
    {
        return "rgb({$color})";
    }
}
