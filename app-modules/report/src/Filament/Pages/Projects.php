<?php

namespace AidingApp\Report\Filament\Pages;

use App\Filament\Clusters\ReportLibrary;
use App\Models\User;
use Filament\Pages\Dashboard;

class Projects extends Dashboard
{
    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Project Management';

    protected static ?string $navigationLabel = 'Projects';

    protected static ?string $title = 'Projects';

    protected static string $routePath = 'projects';

    protected static string $view = 'filament.pages.coming-soon';

    protected static ?int $navigationSort = 90;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('report-library.view-any');
    }
}
