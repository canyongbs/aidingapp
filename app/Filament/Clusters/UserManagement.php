<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class UserManagement extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?string $navigationLabel = 'User Management';

    protected static ?int $navigationSort = 1;
}
