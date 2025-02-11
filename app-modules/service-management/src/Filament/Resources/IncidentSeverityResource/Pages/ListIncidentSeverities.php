<?php

namespace AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource\Pages;

use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource;
use AidingApp\ServiceManagement\Models\IncidentSeverity;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;

class ListIncidentSeverities extends ListRecords
{
    protected static string $resource = IncidentSeverityResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('color')
                    ->label('Color')
                    ->badge()
                    ->color(fn (IncidentSeverity $incidentSeverity) => $incidentSeverity->color->value),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records) {
                            $deletedIncidentSeveritiesCount = IncidentSeverity::query()
                                ->whereKey($records)
                                ->whereDoesntHave('incidents')
                                ->delete();

                            Notification::make()
                                ->title('Deleted ' . $deletedIncidentSeveritiesCount . ' incident severities')
                                ->body(($deletedIncidentSeveritiesCount < $records->count()) ? ($records->count() - $deletedIncidentSeveritiesCount) . ' incident severities were not deleted because they have incidents.' : null)
                                ->success()
                                ->send();
                        })
                        ->fetchSelectedRecords(false),
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
