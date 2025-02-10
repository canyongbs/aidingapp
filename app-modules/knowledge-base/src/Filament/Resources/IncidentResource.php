<?php

namespace AidingApp\KnowledgeBase\Filament\Resources;

use AidingApp\KnowledgeBase\Filament\Resources\IncidentResource\Pages\CreateIncident;
use AidingApp\KnowledgeBase\Filament\Resources\IncidentResource\Pages\EditIncident;
use AidingApp\KnowledgeBase\Filament\Resources\IncidentResource\Pages\ListIncidents;
use AidingApp\KnowledgeBase\Filament\Resources\IncidentResource\Pages\ViewIncident;
use AidingApp\KnowledgeBase\Models\Incident;
use App\Filament\Clusters\KnowledgeManagement;
use Filament\Resources\Resource;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    protected static ?string $cluster = KnowledgeManagement::class;

    protected static ?string $navigationGroup = 'Incident Management';

    protected static ?int $navigationSort = 10;

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
