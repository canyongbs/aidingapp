<?php

namespace AidingApp\Contact\Filament\Resources\OrganizationResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use AidingApp\Contact\Filament\Resources\OrganizationResource;
use AidingApp\Contact\Filament\Resources\OrganizationResource\RelationManagers\ContactsRelationManager;

class ManageContacts extends ManageRelatedRecords
{
    protected static string $resource = OrganizationResource::class;

    protected static string $relationship = 'contacts';

    protected static ?string $navigationLabel = 'Contacts';

    protected static ?string $breadcrumb = 'Contacts';

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public function getRelationManagers(): array
    {
        return [
            ContactsRelationManager::class,
        ];
    }
}
