<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ContractResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Filters\Filter;
use App\Features\ContractManagement;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AidingApp\ServiceManagement\Enums\ContractStatus;
use AidingApp\ServiceManagement\Filament\Resources\ContractResource;

class ListContracts extends ListRecords
{
    protected static string $resource = ContractResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('contractType.name')
                    ->visible(ContractManagement::active())
                    ->label('Contract Type'),
                TextColumn::make('status')
                    ->label('Contract Status'),
                TextColumn::make('start_date')
                    ->label('Start Date'),
                TextColumn::make('end_date')
                    ->label('End Date'),
            ])
            ->filters([
                SelectFilter::make('contract_type')
                    ->visible(ContractManagement::active())
                    ->relationship('contractType', 'name'),
                SelectFilter::make('status')
                    ->options(collect(ContractStatus::cases())->mapWithKeys(fn (ContractStatus $direction) => [$direction->value => \Livewire\str($direction->name)->title()->headline()]))
                    ->query(function (Builder $query, $state) {
                        $today = now();

                        return match ($state['value']) {
                            ContractStatus::Pending->value => $query->where('start_date', '>', $today),
                            ContractStatus::Pending->value => $query->where('start_date', '<=', $today)
                                ->where('end_date', '>=', $today),
                            ContractStatus::Pending->value => $query->where('end_date', '<', $today),
                            default => $query,
                        };
                    }),
                Filter::make('start_date')
                    ->form([
                        DatePicker::make('start_date')
                            ->native(false)
                            ->displayFormat('m/d/Y')
                            ->closeOnDateSelection(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query, $date): Builder => $query->where('start_date', '>=', $date),
                            );
                    }),
                Filter::make('end_date')
                    ->form([
                        DatePicker::make('end_date')
                            ->native(false)
                            ->displayFormat('m/d/Y')
                            ->closeOnDateSelection(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['end_date'],
                                fn (Builder $query, $date): Builder => $query->where('end_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
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
