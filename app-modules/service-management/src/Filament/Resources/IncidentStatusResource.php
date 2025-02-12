<?php

namespace AidingApp\ServiceManagement\Filament\Resources;

use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource\Pages\CreateIncidentStatus;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource\Pages\EditIncidentStatus;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource\Pages\ListIncidentStatuses;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource\Pages\ViewIncidentStatus;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use App\Features\IncidentSeverityStatus;
use App\Filament\Clusters\IncidentManagement;
use Filament\Resources\Resource;

class IncidentStatusResource extends Resource
{
    protected static ?string $model = IncidentStatus::class;

    protected static ?string $cluster = IncidentManagement::class;

    protected static ?string $navigationLabel = 'Statuses';

    protected static ?int $navigationSort = 20;

    public static function canAccess(): bool
    {
        return parent::canAccess() && IncidentSeverityStatus::active();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIncidentStatuses::route('/'),
            'create' => CreateIncidentStatus::route('/create'),
            'view' => ViewIncidentStatus::route('/{record}'),
            'edit' => EditIncidentStatus::route('/{record}/edit'),
        ];
    }
}
