<?php

namespace AidingApp\Contact\Filament\Resources\OrganizationIndustryResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource;

class ViewOrganizationIndustry extends ViewRecord
{
    protected static string $resource = OrganizationIndustryResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name')
                            ->translateLabel(),
                        IconEntry::make('is_default')
                            ->label('Default')
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->boolean(),
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
