<?php

namespace App\Filament\Clusters\ServiceManagement\Pages;

use App\Filament\Clusters\ServiceManagementAdministration;
use Filament\Pages\Page;

class Sequence extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Service Requests';

    protected static ?int $navigationSort = 40;

    protected static ?string $cluster = ServiceManagementAdministration::class;

    protected static string $view = 'filament.pages.coming-soon';
}
