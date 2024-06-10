<?php

namespace AidingApp\Contact\Filament\Resources;

use Laravel\Pennant\Feature;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use AidingApp\Contact\Models\Organization;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AidingApp\Contact\Filament\Resources\OrganizationResource\Pages\EditOrganization;
use AidingApp\Contact\Filament\Resources\OrganizationResource\Pages\ViewOrganization;
use AidingApp\Contact\Filament\Resources\OrganizationResource\Pages\ListOrganizations;
use AidingApp\Contact\Filament\Resources\OrganizationResource\Pages\CreateOrganization;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 21;

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizations::route('/'),
            'create' => CreateOrganization::route('/create'),
            'view' => ViewOrganization::route('/{record}'),
            'edit' => EditOrganization::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
