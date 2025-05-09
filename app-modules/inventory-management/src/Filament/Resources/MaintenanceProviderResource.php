<?php

namespace AidingApp\InventoryManagement\Filament\Resources;

use AidingApp\InventoryManagement\Filament\Resources\MaintenanceProviderResource\Pages\CreateMaintenanceProvider;
use AidingApp\InventoryManagement\Filament\Resources\MaintenanceProviderResource\Pages\ListMaintenanceProviders;
use AidingApp\InventoryManagement\Filament\Resources\MaintenanceProviderResource\Pages\ViewMaintenanceProvider;
use AidingApp\InventoryManagement\Models\MaintenanceProvider;
use App\Filament\Clusters\AssetManagement;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class MaintenanceProviderResource extends Resource
{
    protected static ?string $model = MaintenanceProvider::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Maintenance Providers';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = AssetManagement::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMaintenanceProviders::route('/'),
            'create' => CreateMaintenanceProvider::route('/create'),
            'view' => ViewMaintenanceProvider::route('/{record}/view'),
        ];
    }
}
