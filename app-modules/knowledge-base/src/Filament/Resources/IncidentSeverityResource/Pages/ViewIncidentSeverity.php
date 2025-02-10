<?php

namespace AidingApp\KnowledgeBase\Filament\Resources\IncidentSeverityResource\Pages;

use AidingApp\KnowledgeBase\Filament\Resources\IncidentSeverityResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewIncidentSeverity extends ViewRecord
{
    protected static string $resource = IncidentSeverityResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
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
