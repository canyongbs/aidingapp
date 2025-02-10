<?php

namespace AidingApp\KnowledgeBase\Filament\Resources;

use AidingApp\KnowledgeBase\Filament\Resources\IncidentSeverityResource\Pages\CreateIncidentSeverity;
use AidingApp\KnowledgeBase\Filament\Resources\IncidentSeverityResource\Pages\EditIncidentSeverity;
use AidingApp\KnowledgeBase\Filament\Resources\IncidentSeverityResource\Pages\ListIncidentSeverities;
use AidingApp\KnowledgeBase\Filament\Resources\IncidentSeverityResource\Pages\ViewIncidentSeverity;
use AidingApp\KnowledgeBase\Models\IncidentSeverity;
use App\Filament\Clusters\KnowledgeManagement;
use Filament\Resources\Resource;

class IncidentSeverityResource extends Resource
{
    protected static ?string $model = IncidentSeverity::class;

    protected static ?string $cluster = KnowledgeManagement::class;

    protected static ?string $navigationGroup = 'Incident Management';

    protected static ?string $modelLabel = 'Severities';

    protected static ?int $navigationSort = 20;

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
