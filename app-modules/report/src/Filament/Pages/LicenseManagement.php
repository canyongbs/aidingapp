<?php

namespace AidingApp\Report\Filament\Pages;

use App\Filament\Clusters\ReportLibrary;
use App\Models\User;
use Filament\Pages\Dashboard;

class LicenseManagement extends Dashboard
{
    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Purchasing Management';

    protected static ?string $navigationLabel = 'License Management';

    protected static ?string $title = 'License Management';

    protected static string $routePath = 'license-management';

    protected static string $view = 'filament.pages.coming-soon';

    protected static ?int $navigationSort = 80;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('report-library.view-any');
    }
}
