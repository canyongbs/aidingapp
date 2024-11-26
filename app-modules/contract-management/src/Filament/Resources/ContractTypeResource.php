<?php

namespace AidingApp\ContractManagement\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Clusters\ContractManagement;
use AidingApp\ContractManagement\Models\ContractType;
use App\Features\ContractManagement as FeaturesContractManagement;
use AidingApp\ContractManagement\Filament\Resources\ContractTypeResource\Pages\EditContractType;
use AidingApp\ContractManagement\Filament\Resources\ContractTypeResource\Pages\ListContractTypes;
use AidingApp\ContractManagement\Filament\Resources\ContractTypeResource\Pages\CreateContractType;

class ContractTypeResource extends Resource
{
    protected static ?string $model = ContractType::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $cluster = ContractManagement::class;

    public static function canAccess(): bool
    {
        return FeaturesContractManagement::active() && parent::canAccess();
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
