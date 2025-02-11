<?php

namespace AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource\Pages;

use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;

class ListIncidentStatuses extends ListRecords
{
    protected static string $resource = IncidentStatusResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classification')
                    ->label('Classification')
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records) {
                            $deletedIncidentStatusesCount = IncidentStatus::query()
                                ->whereKey($records)
                                ->whereDoesntHave('incidents')
                                ->delete();

                            Notification::make()
                                ->title('Deleted ' . $deletedIncidentStatusesCount . ' incident statuses')
                                ->body(($deletedIncidentStatusesCount < $records->count()) ? ($records->count() - $deletedIncidentStatusesCount) . ' incident statuses were not deleted because they have incidents.' : null)
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
