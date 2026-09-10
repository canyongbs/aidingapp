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

class SlaPerformanceByAgentTable extends BaseWidget
{
    use InteractsWithSlaReportData;

    public string $cacheTag;

    protected static ?string $heading = 'SLA Performance by Agent';

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 4,
        'md' => 4,
        'lg' => 4,
    ];

    /**
     * @var Collection<int, mixed>|null
     */
    private ?Collection $agentRows = null;

    public function mount(string $cacheTag): void
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget(): void
    {
        $this->agentRows = null;

        $this->dispatch('$refresh');
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(function (int $page, int $recordsPerPage, ?string $sortColumn, ?string $sortDirection): LengthAwarePaginator {
                $rows = $this->getAgentRows();

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
                TextColumn::make('agent')
                    ->label('Agent')
                    ->sortable(),
                TextColumn::make('requests')
                    ->label('Requests')
                    ->sortable(),
                TextColumn::make('responseSla')
                    ->label('Response SLA')
                    ->formatStateUsing(fn (float $state): string => "{$state}%")
                    ->sortable(),
                TextColumn::make('resolutionSla')
                    ->label('Resolution SLA')
                    ->formatStateUsing(fn (float $state): string => "{$state}%")
                    ->sortable(),
                TextColumn::make('slaBreaches')
                    ->label('SLA Breaches')
                    ->sortable(),
                TextColumn::make('avgResponseSeconds')
                    ->label('Avg Response Time')
                    ->formatStateUsing(fn (?int $state): string => $this->formatSlaDuration($state)),
                TextColumn::make('avgResolutionSeconds')
                    ->label('Avg Resolution Time')
                    ->formatStateUsing(fn (?int $state): string => $this->formatSlaDuration($state)),
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
        return 'agentsPage';
    }

    public function exportCsv(): StreamedResponse
    {
        $rows = $this->getAgentRows();

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Agent',
                'Requests',
                'Response SLA',
                'Resolution SLA',
                'SLA Breaches',
                'Avg Response Time',
                'Avg Resolution Time',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['agent'],
                    $row['requests'],
                    "{$row['responseSla']}%",
                    "{$row['resolutionSla']}%",
                    $row['slaBreaches'],
                    $this->formatSlaDuration($row['avgResponseSeconds']),
                    $this->formatSlaDuration($row['avgResolutionSeconds']),
                ]);
            }

            fclose($handle);
        }, 'sla-performance-by-agent.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
        * @return Collection<int, mixed>
     */
    private function getAgentRows(): Collection
    {
        if ($this->agentRows !== null) {
            return $this->agentRows;
        }

        $serviceRequests = $this->slaServiceRequestsQuery()
            ->whereHas('assignedTo')
            ->with($this->slaEagerLoads())
            ->get();

        $agentRows = $serviceRequests
            ->groupBy(fn (ServiceRequest $serviceRequest): ?string => $serviceRequest->assignedTo?->user_id)
            ->filter(fn (Collection $group, ?string $agentId): bool => filled($agentId))
            ->map(function (Collection $group): array {
                $metrics = $this->summarizeSlaMetrics($group);

                $first = $group->first();
                assert($first instanceof ServiceRequest);

                return [
                    'id' => $first->assignedTo?->user_id,
                    'agent' => $first->assignedTo?->user->name ?? 'Unassigned',
                    'requests' => $metrics['total'],
                    'responseSla' => $metrics['response_compliance_percentage'],
                    'resolutionSla' => $metrics['resolution_compliance_percentage'],
                    'slaBreaches' => $metrics['breaches'],
                    'avgResponseSeconds' => $metrics['average_response_seconds'],
                    'avgResolutionSeconds' => $metrics['average_resolution_seconds'],
                ];
            })
            ->sortByDesc('requests')
            ->values();

        $this->agentRows = $agentRows;

        return $agentRows;
    }
}
