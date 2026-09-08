<?php

namespace AidingApp\Group\Filament\Resources\Groups\Pages;

use AidingApp\Group\Filament\Resources\Groups\GroupResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewGroup extends ViewRecord
{
    protected static string $resource = GroupResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Properties')
                    ->schema([
                        TextEntry::make('name')
                            ->columnSpanFull(),
                        TextEntry::make('description')
                            ->placeholder('N/A')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
