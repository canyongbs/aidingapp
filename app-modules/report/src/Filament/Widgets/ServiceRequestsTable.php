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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Report\Filament\Exports\ServiceRequestsExporter;
use AidingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class ServiceRequestsTable extends BaseWidget
{
    use InteractsWithPageFilters;

    public string $cacheTag;

    protected static ?string $heading = 'Service Requests';

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 4,
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
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        return $table
            ->query(
                ServiceRequest::query()
                    ->with([
                        'priority.type',
                        'status',
                        'respondent',
                        'assignedTo.user',
                    ])
                    ->when(
                        $startDate && $endDate,
                        fn (Builder $query) => $query->whereBetween('created_at', [$startDate, $endDate])
                    )
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('service_request_number')
                    ->label('Request #')
                    ->searchable(['service_request_number', 'title'])
                    ->url(fn (ServiceRequest $record): string => ServiceRequestResource::getUrl('view', ['record' => $record]))
                    ->description(fn (ServiceRequest $record): string => Str::limit($record->title, 40)),
                TextColumn::make('priority.type.name')
                    ->label('Type')
                    ->searchable(),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->searchable()
                    ->badge()
                    ->color(fn (ServiceRequest $record): string => $record->status->color->value),
                TextColumn::make('respondent.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (ServiceRequest $record) => $record->respondent->{$record->respondent::displayNameKey()})
                    ->searchable(
                        query: fn (Builder $query, $search) => $query->whereHas(
                            'respondent',
                            fn (Builder $query) => $query->whereRaw('lower(full_name) like ?', ['%' . strtolower($search) . '%'])
                        )
                    )
                    ->url(fn (ServiceRequest $record): string => ContactResource::getUrl('view', ['record' => $record->respondent]))
                    ->description(fn (ServiceRequest $record): ?string => $record->respondent->organization->name ?? null),
                TextColumn::make('assignedTo.user.name')
                    ->label('Assigned To')
                    ->searchable()
                    ->placeholder('Unassigned'),
                TextColumn::make('created_at')
                    ->label('Date/Time Opened')
                    ->dateTime('m/d/Y g:i A'),
                TextColumn::make('resolved_at')
                    ->label('Date/Time Closed')
                    ->getStateUsing(function (ServiceRequest $record): ?string {
                        if ($record->status->classification->value !== 'closed') {
                            return null;
                        }
                        $resolvedAt = $record->getResolvedAt();
                        return $resolvedAt->format('m/d/Y g:i A');
                    })
                    ->placeholder('Not closed'),
                TextColumn::make('age')
                    ->label('Age')
                    ->getStateUsing(function (ServiceRequest $record): ?string {
                        if ($record->status->classification->value !== 'closed') {
                            return null;
                        }
                        
                        $interval = $record->created_at->diff($record->getResolvedAt());
                        $days = $interval->days;
                        $hours = $interval->h;
                        $minutes = $interval->i;
                        
                        if ($days > 0) {
                            return "{$days}d {$hours}h {$minutes}m";
                        } elseif ($hours > 0) {
                            return "{$hours}h {$minutes}m";
                        } else {
                            return "{$minutes}m";
                        }
                    })
                    ->placeholder(''),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->relationship('priority.type', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('assigned_to')
                    ->relationship('assignedTo.user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Export')
                    ->exporter(ServiceRequestsExporter::class),
            ])
            ->paginated([5, 10, 15, 20, 25])
            ->defaultSort('created_at', 'desc');
    }
}