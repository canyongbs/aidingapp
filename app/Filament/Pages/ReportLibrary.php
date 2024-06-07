<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ReportLibrary extends Page
{
    protected static ?string $navigationGroup = 'Reporting';

    protected static ?int $navigationSort = 30;

    protected static ?string $navigationLabel = 'Report Library';

    protected static string $view = 'filament.pages.coming-soon';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('report-library.view-any');
    }
}