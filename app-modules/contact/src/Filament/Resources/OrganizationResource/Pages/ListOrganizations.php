<?php

namespace AidingApp\Contact\Filament\Resources\OrganizationResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AidingApp\Contact\Filament\Resources\OrganizationResource;

class ListOrganizations extends ListRecords
{
    protected static string $resource = OrganizationResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->label('Organization Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('website')
                    ->label('Website')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('industry.name') 
                    ->label('Industry')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
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
