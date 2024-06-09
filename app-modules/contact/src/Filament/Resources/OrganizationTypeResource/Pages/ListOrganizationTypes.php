<?php

namespace AidingApp\Contact\Filament\Resources\OrganizationTypeResource\Pages;


use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AidingApp\Contact\Filament\Resources\OrganizationTypeResource;
use Filament\Actions\CreateAction;
use Filament\Tables\Columns\IconColumn;

class ListOrganizationTypes extends ListRecords
{
    protected static string $resource = OrganizationTypeResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
            ])

            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
