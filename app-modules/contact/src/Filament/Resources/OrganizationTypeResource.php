<?php

namespace AidingApp\Contact\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Clusters\ContactManagement;
use AidingApp\Contact\Models\OrganizationType;
use AidingApp\Contact\Filament\Resources\OrganizationTypeResource\Pages\EditOrganizationType;
use AidingApp\Contact\Filament\Resources\OrganizationTypeResource\Pages\ViewOrganizationType;
use AidingApp\Contact\Filament\Resources\OrganizationTypeResource\Pages\ListOrganizationTypes;
use AidingApp\Contact\Filament\Resources\OrganizationTypeResource\Pages\CreateOrganizationType;
use Laravel\Pennant\Feature;

class OrganizationTypeResource extends Resource
{
    protected static ?string $model = OrganizationType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Org Types';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = ContactManagement::class;

    public static function getRelations(): array
    {
        return [
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => ListOrganizationTypes::route('/'),
            'create' => CreateOrganizationType::route('/create'),
            'view' => ViewOrganizationType::route('/{record}'),
            'edit' => EditOrganizationType::route('/{record}/edit'),
        ];
    }
}
