<?php

namespace AidingApp\ContractManagement\Filament\Resources;

use Filament\Resources\Resource;
use App\Features\ContractManagement;
use AidingApp\ContractManagement\Models\Contract;
use AidingApp\ContractManagement\Filament\Resources\ContractResource\Pages\EditContract;
use AidingApp\ContractManagement\Filament\Resources\ContractResource\Pages\ViewContract;
use AidingApp\ContractManagement\Filament\Resources\ContractResource\Pages\ListContracts;
use AidingApp\ContractManagement\Filament\Resources\ContractResource\Pages\CreateContract;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?string $navigationLabel = 'Contract Management';

    protected static ?int $navigationSort = 40;

    public static function canAccess(): bool
    {
        return ContractManagement::active() && parent::canAccess();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContracts::route('/'),
            'create' => CreateContract::route('/create'),
            'view' => ViewContract::route('/{record}'),
            'edit' => EditContract::route('/{record}/edit'),
        ];
    }
}
