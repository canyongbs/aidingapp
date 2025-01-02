<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\ContractManagement\Filament\Resources\ContractResource\Pages;

use AidingApp\ContractManagement\Enums\ContractStatus;
use AidingApp\ContractManagement\Filament\Resources\ContractResource;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListContracts extends ListRecords
{
    protected static string $resource = ContractResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('contractType.name')
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
                    ->relationship('contractType', 'name'),
                SelectFilter::make('status')
                    ->options(collect(ContractStatus::cases())->mapWithKeys(fn (ContractStatus $contractStatus) => [
                        $contractStatus->value => $contractStatus->getLabel(),
                    ]))
                    ->query(function (Builder $query, $state) {
                        $today = now();

                        return match ($state['value']) {
                            ContractStatus::Pending->value => $query->where('start_date', '>', $today),
                            ContractStatus::Active->value => $query->where('start_date', '<=', $today)
                                ->where('end_date', '>=', $today),
                            ContractStatus::Expired->value => $query->where('end_date', '<', $today),
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
