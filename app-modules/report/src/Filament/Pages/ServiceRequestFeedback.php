<?php

namespace AidingApp\Report\Filament\Pages;

use AidingApp\Report\Abstract\ServiceRequestFeedbackReport;
use AidingApp\Report\Filament\Widgets\RefreshWidget;
use AidingApp\Report\Filament\Widgets\ServiceRequestFeedbackStats;
use AidingApp\Report\Filament\Widgets\ServiceRequestFeedbackTable;
use App\Filament\Clusters\ReportLibrary;
use App\Models\User;

class ServiceRequestFeedback extends ServiceRequestFeedbackReport
{
    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?string $navigationLabel = 'SR Feedback';

    protected static ?string $title = 'Service Request Feedback';

    protected static string $routePath = 'sr-feedback';

    protected static ?int $navigationSort = 20;

    protected string $cacheTag = 'report-service-request-feedback';

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
            ServiceRequestFeedbackStats::make(['cacheTag' => $this->cacheTag]),
            ServiceRequestFeedbackTable::make(['cacheTag' => $this->cacheTag]),
        ];
    }

    public function getColumns(): int|string|array
    {
        return [
            'sm' => 2,
            'md' => 4,
            'lg' => 4,
        ];
    }

    public function getWidgetData(): array
    {
        return [
            'filters' => $this->filters,
        ];
    }
}
