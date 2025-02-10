<?php

namespace AidingApp\KnowledgeBase\Filament\Resources\IncidentResource\Pages;

use AidingApp\KnowledgeBase\Filament\Resources\IncidentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIncidents extends ListRecords
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
