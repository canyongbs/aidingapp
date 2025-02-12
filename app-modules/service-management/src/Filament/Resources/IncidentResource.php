<?php

namespace AidingApp\ServiceManagement\Filament\Resources;

use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\CreateIncident;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\EditIncident;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\ListIncidents;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\ViewIncident;
use AidingApp\ServiceManagement\Models\Incident;
use App\Features\IncidentSeverityStatus;
use Filament\Resources\Resource;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    protected static ?string $navigationLabel = 'Incident Management';

    protected static ?string $navigationIcon = 'heroicon-m-clipboard-document-list';

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?int $navigationSort = 60;

    protected static ?string $breadcrumb = 'Incident Management';

    public static function canAccess(): bool
    {
        return IncidentSeverityStatus::active() && parent::canAccess();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIncidents::route('/'),
            'create' => CreateIncident::route('/create'),
            'view' => ViewIncident::route('/{record}'),
            'edit' => EditIncident::route('/{record}/edit'),
        ];
    }
}
