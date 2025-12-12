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

namespace AidingApp\Report\Filament\Widgets;

use AidingApp\InventoryManagement\Enums\MaintenanceActivityStatus;
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\Report\Filament\Exports\AssetsExporter;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class AssetsTable extends BaseWidget
{
    public string $cacheTag;

    protected static ?string $heading = 'Assets';

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 4,
        'lg' => 4,
    ];

    public function mount(string $cacheTag): void
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget(): void
    {
        $this->dispatch('$refresh');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Asset::query()
                    ->with(['type', 'status', 'location', 'maintenanceActivities', 'latestCheckOut'])
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('type.name')
                    ->label('Type')
                    ->searchable(),
                TextColumn::make('status.name')
                    ->label('Status'),
                TextColumn::make('deployment_status')
                    ->label('Deployment')
                    ->getStateUsing(function (Asset $record): string {
                        $hasActiveMaintenance = $record->maintenanceActivities()
                            ->whereNotIn('status', [
                                MaintenanceActivityStatus::Completed,
                                MaintenanceActivityStatus::Canceled,
                            ])
                            ->exists();

                        if ($hasActiveMaintenance) {
                            return 'Maintenance';
                        }

                        $isCheckedOut = $record->latestCheckOut && is_null($record->latestCheckOut->asset_check_in_id);

                        if ($isCheckedOut) {
                            return 'Checked Out';
                        }

                        return 'Bench Stock';
                    })
                    ->badge(),
                TextColumn::make('purchase_date')
                    ->label('Purchase Date')
                    ->date(),
                TextColumn::make('purchase_age')
                    ->label('Device Age'),
            ])
            ->filters([
                SelectFilter::make('name')
                    ->label('Name')
                    ->options(fn (): array => Asset::query()->orderBy('name')->limit(50)->pluck('name', 'name')->toArray())
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('type')
                    ->label('Type')
                    ->relationship('type', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->relationship('status', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('deployment_status')
                    ->label('Deployment')
                    ->options([
                        'Maintenance' => 'Maintenance',
                        'Checked Out' => 'Checked Out',
                        'Bench Stock' => 'Bench Stock',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['values'])) {
                            return $query;
                        }

                        return $query->where(function (Builder $query) use ($data) {
                            foreach ($data['values'] as $status) {
                                $query->orWhere(function (Builder $subQuery) use ($status) {
                                    if ($status === 'Maintenance') {
                                        $subQuery->whereHas('maintenanceActivities', function (Builder $maintenanceQuery) {
                                            $maintenanceQuery->whereNotIn('status', [
                                                MaintenanceActivityStatus::Completed,
                                                MaintenanceActivityStatus::Canceled,
                                            ]);
                                        });
                                    } elseif ($status === 'Checked Out') {
                                        $subQuery->whereHas('latestCheckOut', function (Builder $checkoutQuery) {
                                            $checkoutQuery->whereNull('asset_check_in_id');
                                        })->whereDoesntHave('maintenanceActivities', function (Builder $maintenanceQuery) {
                                            $maintenanceQuery->whereNotIn('status', [
                                                MaintenanceActivityStatus::Completed,
                                                MaintenanceActivityStatus::Canceled,
                                            ]);
                                        });
                                    } elseif ($status === 'Bench Stock') {
                                        $subQuery->whereDoesntHave('maintenanceActivities', function (Builder $maintenanceQuery) {
                                            $maintenanceQuery->whereNotIn('status', [
                                                MaintenanceActivityStatus::Completed,
                                                MaintenanceActivityStatus::Canceled,
                                            ]);
                                        })->where(function (Builder $checkoutQuery) {
                                            $checkoutQuery->whereDoesntHave('latestCheckOut')
                                                ->orWhereHas('latestCheckOut', function (Builder $checkinQuery) {
                                                    $checkinQuery->whereNotNull('asset_check_in_id');
                                                });
                                        });
                                    }
                                });
                            }
                        });
                    })
                    ->multiple(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Export')
                    ->exporter(AssetsExporter::class),
            ])
            ->paginated([5]);
    }
}
