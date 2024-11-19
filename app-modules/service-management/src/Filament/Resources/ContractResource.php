<?php

namespace AidingApp\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use App\Features\ContractManagement;
use AidingApp\ServiceManagement\Models\Contract;
use AidingApp\ServiceManagement\Filament\Resources\ContractResource\Pages\EditContract;
use AidingApp\ServiceManagement\Filament\Resources\ContractResource\Pages\ViewContract;
use AidingApp\ServiceManagement\Filament\Resources\ContractResource\Pages\ListContracts;
use AidingApp\ServiceManagement\Filament\Resources\ContractResource\Pages\CreateContract;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?string $navigationLabel = 'Contract Management';

    protected static ?int $navigationSort = 40;

    public static function canAccess(): bool
    {
        return parent::canAccess() && ContractManagement::active();
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
