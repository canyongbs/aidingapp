<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ProductResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AidingApp\ServiceManagement\Filament\Resources\ProductResource;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Product Name'),
                        TextEntry::make('url')
                            ->label('Product Link')
                            ->url(fn ($record) => $record->url, true)
                            ->openUrlInNewTab(),
                        TextEntry::make('description')
                            ->label('Description'),
                        TextEntry::make('version')
                            ->label('Version'),
                        TextEntry::make('additional_notes')
                            ->label('Additional Notes'),
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
