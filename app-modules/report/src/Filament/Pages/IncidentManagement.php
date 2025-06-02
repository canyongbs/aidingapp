<?php

namespace AidingApp\Report\Filament\Pages;

use App\Filament\Clusters\ReportLibrary;
use App\Models\User;
use Filament\Pages\Dashboard;

class IncidentManagement extends Dashboard
{
    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?string $navigationLabel = 'Incident Management';

    protected static ?string $title = 'Incident Management';

    protected static string $routePath = 'incident-management';

    protected static string $view = 'filament.pages.coming-soon';

    protected static ?int $navigationSort = 40;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('report-library.view-any');
    }
}
