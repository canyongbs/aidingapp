<?php

namespace AidingApp\KnowledgeBase\Filament\Resources;

use AidingApp\KnowledgeBase\Filament\Resources\IncidentStatusResource\Pages\CreateIncidentStatus;
use AidingApp\KnowledgeBase\Filament\Resources\IncidentStatusResource\Pages\EditIncidentStatus;
use AidingApp\KnowledgeBase\Filament\Resources\IncidentStatusResource\Pages\ListIncidentStatuses;
use AidingApp\KnowledgeBase\Filament\Resources\IncidentStatusResource\Pages\ViewIncidentStatus;
use AidingApp\KnowledgeBase\Models\IncidentStatus;
use App\Filament\Clusters\KnowledgeManagement;
use Filament\Resources\Resource;

class IncidentStatusResource extends Resource
{
    protected static ?string $model = IncidentStatus::class;

    protected static ?string $cluster = KnowledgeManagement::class;

    protected static ?string $navigationGroup = 'Incident Management';

    // protected static ?string $navigationLabel = 'Statuses';

    protected static ?string $modelLabel = 'Statuses';

    protected static ?int $navigationSort = 30;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
