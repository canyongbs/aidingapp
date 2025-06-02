<?php

namespace AidingApp\Report\Filament\Pages;

use App\Filament\Clusters\ReportLibrary;
use App\Models\User;
use Filament\Pages\Dashboard;

class ContractManagement extends Dashboard
{
    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Purchasing Management';

    protected static ?string $navigationLabel = 'Contract Management';

    protected static ?string $title = 'Contract Management';

    protected static string $routePath = 'contract-management';

    protected static string $view = 'filament.pages.coming-soon';

    protected static ?int $navigationSort = 70;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('report-library.view-any');
    }
}
