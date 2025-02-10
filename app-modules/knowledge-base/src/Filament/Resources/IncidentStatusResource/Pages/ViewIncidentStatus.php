<?php

namespace AidingApp\KnowledgeBase\Filament\Resources\IncidentStatusResource\Pages;

use AidingApp\KnowledgeBase\Filament\Resources\IncidentStatusResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewIncidentStatus extends ViewRecord
{
    protected static string $resource = IncidentStatusResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('classification')
                            ->label('Classification'),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
