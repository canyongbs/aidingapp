<?php

namespace AidingApp\InventoryManagement\Filament\Resources\MaintenanceProviderResource\Pages;

use AidingApp\InventoryManagement\Filament\Resources\MaintenanceProviderResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewMaintenanceProvider extends ViewRecord
{
    protected static string $resource = MaintenanceProviderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name'),
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
