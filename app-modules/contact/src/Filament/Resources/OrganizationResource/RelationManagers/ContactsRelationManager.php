<?php

namespace AidingApp\Contact\Filament\Resources\OrganizationResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Tables\Actions\AssociateAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DissociateAction;
use Filament\Tables\Actions\DissociateBulkAction;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use Filament\Resources\RelationManagers\RelationManager;

class ContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                TextColumn::make('full_name'),
                TextColumn::make('email'),
                TextColumn::make('mobile'),
            ])
            ->headerActions([
                AssociateAction::make(),
            ])
            ->actions([
                DissociateAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                ]),
            ]);
    }
}
