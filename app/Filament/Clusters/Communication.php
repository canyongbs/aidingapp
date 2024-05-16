<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Communication extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?string $navigationLabel = 'Communication Settings';

    protected static ?int $navigationSort = 40;
}
