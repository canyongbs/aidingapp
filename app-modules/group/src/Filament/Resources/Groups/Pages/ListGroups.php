<?php

namespace AidingApp\Group\Filament\Resources\Groups\Pages;

use AidingApp\Group\Filament\Resources\Groups\GroupResource;
use AidingApp\Group\Models\Group;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ListGroups extends ListRecords
{
    protected static string $resource = GroupResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->description(fn (Group $record): ?string => $record->description)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label('Members')
                    ->counts('users')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->placeholder('N/A')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
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
