<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Report\Filament\Widgets;

use AidingApp\Report\Filament\Widgets\Concerns\InteractsWithSlaReportData;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SlaBreachesByServiceRequestTypeTable extends BaseWidget
{
    use InteractsWithSlaReportData;

    public string $cacheTag;

    protected static ?string $heading = 'SLA Breaches by Service Request Type';

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 4,
        'md' => 4,
        'lg' => 4,
    ];

    /**
     * @var Collection<int, array{id: mixed, type: string, sla_requests: int, response_breaches: int, resolution_breaches: int, response_compliance: float, resolution_compliance: float}>|null
     */
    private ?Collection $typeRows = null;

    public function mount(string $cacheTag): void
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget(): void
    {
        $this->typeRows = null;

        $this->dispatch('$refresh');
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(function (int $page, int $recordsPerPage, ?string $sortColumn, ?string $sortDirection): LengthAwarePaginator {
                $rows = $this->getTypeRows();

                if (filled($sortColumn)) {
                    $rows = $rows->sortBy($sortColumn, SORT_REGULAR, $sortDirection === 'desc')->values();
                }

                return new LengthAwarePaginator(
                    items: $rows->forPage($page, $recordsPerPage)->values(),
                    total: $rows->count(),
                    perPage: $recordsPerPage,
                    currentPage: $page,
                    options: ['pageName' => $this->getTablePaginationPageName()],
                );
            })
            ->columns([
                TextColumn::make('type')
                    ->label('Service Request Type')
                    ->sortable(),
                TextColumn::make('sla_requests')
                    ->label('SLA Requests')
                    ->sortable(),
                TextColumn::make('response_breaches')
                    ->label('Response Breaches')
                    ->sortable(),
                TextColumn::make('resolution_breaches')
                    ->label('Resolution Breaches')
                    ->sortable(),
                TextColumn::make('response_compliance')
                    ->label('Response Compliance')
                    ->formatStateUsing(fn (float $state): string => "{$state}%")
                    ->sortable(),
                TextColumn::make('resolution_compliance')
                    ->label('Resolution Compliance')
                    ->formatStateUsing(fn (float $state): string => "{$state}%")
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->action(fn (): StreamedResponse => $this->exportCsv()),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }

    public function getTablePaginationPageName(): string
    {
        return 'typesPage';
    }

    public function exportCsv(): StreamedResponse
    {
        $rows = $this->getTypeRows();

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Service Request Type',
                'SLA Requests',
                'Response Breaches',
                'Resolution Breaches',
                'Response Compliance',
                'Resolution Compliance',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['type'],
                    $row['sla_requests'],
                    $row['response_breaches'],
                    $row['resolution_breaches'],
                    "{$row['response_compliance']}%",
                    "{$row['resolution_compliance']}%",
                ]);
            }

            fclose($handle);
        }, 'sla-breaches-by-service-request-type.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * @return Collection<int, array{id: mixed, type: string, sla_requests: int, response_breaches: int, resolution_breaches: int, response_compliance: float, resolution_compliance: float}>
     */
    private function getTypeRows(): Collection
    {
        if ($this->typeRows !== null) {
            return $this->typeRows;
        }

        $serviceRequests = $this->slaServiceRequestsQuery()
            ->whereHas('priority.type')
            ->with($this->slaEagerLoads())
            ->get();

        return $this->typeRows = $serviceRequests
            ->groupBy(fn (ServiceRequest $serviceRequest): ?string => $serviceRequest->priority?->type?->getKey())
            ->filter(fn (Collection $group, ?string $typeId): bool => filled($typeId))
            ->map(function (Collection $group) {
                $metrics = $this->summarizeSlaMetrics($group);

                /** @var ServiceRequest $first */
                $first = $group->first();

                return [
                    'id' => $first->priority?->type?->getKey(),
                    'type' => $first->priority?->type->name ?? 'Unknown',
                    'sla_requests' => $metrics['sla_total'],
                    'response_breaches' => $metrics['response_breaches'],
                    'resolution_breaches' => $metrics['resolution_breaches'],
                    'response_compliance' => $metrics['response_compliance_percentage'],
                    'resolution_compliance' => $metrics['resolution_compliance_percentage'],
                ];
            })
            ->sortByDesc('sla_requests')
            ->values();
    }
}
