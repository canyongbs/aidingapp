<?php

namespace App\Filament\Resources\TagResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use App\Filament\Resources\TagResource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Relations\Relation;

class ListTags extends ListRecords
{
    protected static string $resource = TagResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('type')
                    ->formatStateUsing(
                        fn ($state): string => Relation::getMorphedModel($state)::getTagLabel()
                    ),
            ])
            ->actions([
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
