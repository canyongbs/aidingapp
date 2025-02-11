<?php

namespace AidingApp\ServiceManagement\Filament\Resources;

use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource\Pages\CreateIncidentSeverity;
use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource\Pages\EditIncidentSeverity;
use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource\Pages\ListIncidentSeverities;
use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource\Pages\ViewIncidentSeverity;
use AidingApp\ServiceManagement\Models\IncidentSeverity;
use App\Filament\Clusters\IncidentManagement;
use Filament\Resources\Resource;

class IncidentSeverityResource extends Resource
{
    protected static ?string $model = IncidentSeverity::class;

    protected static ?string $cluster = IncidentManagement::class;

    protected static ?string $navigationLabel = 'Severities';

    protected static ?int $navigationSort = 10;

    public static function getPages(): array
    {
        return [
            'index' => ListIncidentSeverities::route('/'),
            'create' => CreateIncidentSeverity::route('/create'),
            'view' => ViewIncidentSeverity::route('/{record}'),
            'edit' => EditIncidentSeverity::route('/{record}/edit'),
        ];
    }
}
