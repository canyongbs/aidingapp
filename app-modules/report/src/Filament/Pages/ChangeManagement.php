<?php

namespace AidingApp\Report\Filament\Pages;

use App\Filament\Clusters\ReportLibrary;
use App\Models\User;
use Filament\Pages\Dashboard;

class ChangeManagement extends Dashboard
{
    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?string $navigationLabel = 'Change Management';

    protected static ?string $title = 'Change Management';

    protected static string $routePath = 'change-management';

    protected static string $view = 'filament.pages.coming-soon';

    protected static ?int $navigationSort = 50;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('report-library.view-any');
    }
}
