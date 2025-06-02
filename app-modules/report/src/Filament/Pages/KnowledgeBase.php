<?php

namespace AidingApp\Report\Filament\Pages;

use App\Filament\Clusters\ReportLibrary;
use App\Models\User;
use Filament\Pages\Dashboard;

class KnowledgeBase extends Dashboard
{
    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?string $navigationLabel = 'Knowledge Base';

    protected static ?string $title = 'Knowledge Base';

    protected static string $routePath = 'knowledge-base';

    protected static string $view = 'filament.pages.coming-soon';

    protected static ?int $navigationSort = 20;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('report-library.view-any');
    }
}
