<?php

namespace AidingApp\Report\Filament\Pages;

use AidingApp\Report\Filament\Widgets\RefreshWidget;
use App\Filament\Clusters\ReportLibrary;
use App\Filament\Pages\Dashboard;
use App\Models\User;
use Filament\Pages\Page;

class ServiceRequestFeedback extends Dashboard
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
        $user = auth()->user();

        assert($user instanceof User);

        return $user->can('report-library.view-any');
    }

    public function getWidgets(): array
    {
        return [
            RefreshWidget::make(['cacheTag' => $this->cacheTag]),
            //row 1: tickets (count), responses (count), csat avg, nps avg --> look at ServiceRequestStats
            //row 2: table
            //service req #, type, csat, nps, related to, assigned to, sla response, sla resolution, created
            //searching: service req #, type, assigned to, related to
            //filters: date, service request type (default all & multiselect)
            //feature flag
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
}
