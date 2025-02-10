<?php

namespace AidingApp\KnowledgeBase\Filament\Resources\IncidentResource\Pages;

use AidingApp\KnowledgeBase\Filament\Resources\IncidentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIncident extends EditRecord
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
