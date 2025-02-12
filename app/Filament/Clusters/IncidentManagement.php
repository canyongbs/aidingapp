<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class IncidentManagement extends Cluster
{
    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?int $navigationSort = 50;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $title = 'Incident Management';
}
