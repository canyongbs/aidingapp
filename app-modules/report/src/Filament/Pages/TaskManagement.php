<?php


namespace AidingApp\Report\Filament\Pages;

use AidingApp\Report\Abstract\EngagementReport;
use AidingApp\Report\Filament\Widgets\MostRecentTasksTable;
use AidingApp\Report\Filament\Widgets\RefreshWidget;
use AidingApp\Report\Filament\Widgets\TaskCumulativeCountLineChart;
use AidingApp\Report\Filament\Widgets\TaskStats;
use App\Filament\Clusters\ReportLibrary;

class TaskManagement extends EngagementReport
{
    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Product Features';

    protected static ?string $navigationLabel = 'Tasks Management';

    protected static ?string $title = 'Tasks (Overview)';

    protected static string $routePath = 'tasks';

    protected static ?int $navigationSort = 10;

    protected $cacheTag = 'report-tasks';

    public function getWidgets(): array
    {
        return [
            RefreshWidget::make(['cacheTag' => $this->cacheTag]),
            TaskStats::make(['cacheTag' => $this->cacheTag]),
            TaskCumulativeCountLineChart::make(['cacheTag' => $this->cacheTag]),
            MostRecentTasksTable::make(['cacheTag' => $this->cacheTag]),
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
