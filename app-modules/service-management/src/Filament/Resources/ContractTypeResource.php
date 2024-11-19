<?php

namespace AidingApp\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Clusters\ContractManagement;
use AidingApp\ServiceManagement\Models\ContractType;
use App\Features\ContractManagement as FeaturesContractManagement;
use AidingApp\ServiceManagement\Filament\Resources\ContractTypeResource\Pages\EditContractType;
use AidingApp\ServiceManagement\Filament\Resources\ContractTypeResource\Pages\ListContractTypes;
use AidingApp\ServiceManagement\Filament\Resources\ContractTypeResource\Pages\CreateContractType;

class ContractTypeResource extends Resource
{
    protected static ?string $model = ContractType::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $cluster = ContractManagement::class;

    public static function canAccess(): bool
    {
        return parent::canAccess() && FeaturesContractManagement::active();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContractTypes::route('/'),
            'create' => CreateContractType::route('/create'),
            'edit' => EditContractType::route('/{record}/edit'),
        ];
    }
}
