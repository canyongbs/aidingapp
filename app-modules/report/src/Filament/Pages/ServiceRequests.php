<?php

namespace AidingApp\Report\Filament\Pages;

use Filament\Pages\Dashboard;
use App\Filament\Clusters\ReportLibrary;
use AidingApp\Report\Filament\Widgets\RefreshWidget;
use AidingApp\Report\Filament\Widgets\ServiceRequestsStats;
use AidingApp\Report\Filament\Widgets\ServiceRequestStatusDistributionDonutChart;
use AidingApp\Report\Filament\Widgets\ServiceRequestPriorityDistributionDonutChart;
use AidingApp\Report\Filament\Widgets\ServiceRequestsOverTimeLineChart;

class ServiceRequests extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Service Requests';

    protected static ?string $navigationLabel = 'Service Requests';

    protected static ?string $title = 'Service Requests';

    protected static string $routePath = 'service-requests';

    protected static ?int $navigationSort = 10;

    protected $cacheTag = 'report-service-requests';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('report-library.view-any');
    }

    public function getWidgets(): array
    {
        return [
            RefreshWidget::make(['cacheTag' => $this->cacheTag]),
            ServiceRequestsStats::make(['cacheTag' => $this->cacheTag]),
            ServiceRequestStatusDistributionDonutChart::make(['cacheTag' => $this->cacheTag]),
            ServiceRequestPriorityDistributionDonutChart::make(['cacheTag' => $this->cacheTag]),
            ServiceRequestsOverTimeLineChart::make(['cacheTag' => $this->cacheTag]),
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 2,
            'md' => 4,
            'lg' => 4,
        ];
    }
}
