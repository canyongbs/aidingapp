<?php

namespace AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages;

use AidingApp\ServiceManagement\Filament\Resources\IncidentResource;
use AidingApp\ServiceManagement\Models\Incident;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Table;

class ListIncidents extends ListRecords
{
    protected static string $resource = IncidentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                ColorColumn::make('severity.rgb_color')
                    ->label('Severity')
                    ->tooltip(fn (Incident $record) => $record->severity->name),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignedTeam.name')
                    ->label('Assigned Team')
                    ->searchable()
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
