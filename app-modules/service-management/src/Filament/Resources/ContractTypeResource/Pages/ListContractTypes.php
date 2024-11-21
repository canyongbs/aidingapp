<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ContractTypeResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AidingApp\ServiceManagement\Filament\Resources\ContractTypeResource;
use Filament\Tables\Actions\DeleteAction;

class ListContractTypes extends ListRecords
{
    protected static string $resource = ContractTypeResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                IconColumn::make('is_default')->label('Is Default')->boolean(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'ASC')
            ->reorderable('order');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
