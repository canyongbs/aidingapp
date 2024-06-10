<?php

namespace AidingApp\Contact\Filament\Resources;

use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource\Pages;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource\Pages\CreateOrganizationIndustry;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource\Pages\EditOrganizationIndustry;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource\Pages\ListOrganizationIndustries;
use AidingApp\Contact\Filament\Resources\OrganizationIndustryResource\Pages\ViewOrganizationIndustry;
use AidingApp\Contact\Models\OrganizationIndustry;
use App\Filament\Clusters\ContactManagement;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Laravel\Pennant\Feature;

class OrganizationIndustryResource extends Resource
{
    protected static ?string $model = OrganizationIndustry::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Org Industries';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = ContactManagement::class;

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizationIndustries::route('/'),
            'create' => CreateOrganizationIndustry::route('/create'),
            'view' => ViewOrganizationIndustry::route('/{record}'),
            'edit' => EditOrganizationIndustry::route('/{record}/edit'),
        ];
    }
}
