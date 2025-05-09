<?php

namespace AidingApp\InventoryManagement\Filament\Resources\MaintenanceProviderResource\Pages;

use AidingApp\InventoryManagement\Filament\Resources\MaintenanceProviderResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateMaintenanceProvider extends CreateRecord
{
    protected static string $resource = MaintenanceProviderResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
