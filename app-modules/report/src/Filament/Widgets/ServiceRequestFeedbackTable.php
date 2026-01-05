<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AidingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use AidingApp\ServiceManagement\Enums\SlaComplianceStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class ServiceRequestFeedbackTable extends BaseWidget
{
    use InteractsWithPageFilters;

    public string $cacheTag;

    protected static ?string $heading = 'Service Request Feedback';

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
    public function refreshWidget(): void {}

    /** @return list<string>|null */
    public function getServiceRequestTypes(): ?array
    {
        $types = $this->filters['serviceRequestTypes'] ?? null;

        return filled($types) ? (array) $types : null;
    }

    public function table(Table $table): Table
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $types = $this->getServiceRequestTypes();

        return $table
            ->query(
                ServiceRequestFeedback::query()
                    ->with([
                        'serviceRequest.priority.type',
                        'serviceRequest.priority.sla',
                        'serviceRequest.respondent',
                        'serviceRequest.assignedTo.user',
                    ])
                    ->when(
                        $startDate && $endDate,
                        fn (Builder $query) => $query->whereBetween('service_request_feedback.created_at', [$startDate, $endDate])
                    )
                    ->when(
                        $types,
                        fn (Builder $query) => $query->whereHas('serviceRequest.priority.type', function (Builder $query) use ($types) {
                            $query->whereIn('id', $types);
                        })
                    )->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('serviceRequest.service_request_number')
                    ->label('Service Request #')
                    ->searchable(),
                TextColumn::make('serviceRequest.priority.type.name')
                    ->label('Type')
                    ->searchable(),
                TextColumn::make('csat_answer')
                    ->label('CSAT'),
                TextColumn::make('nps_answer')
                    ->label('NPS'),
                TextColumn::make('serviceRequest.respondent.display_name')
                    ->label('Related To')
                    ->searchable('full_name')
                    ->getStateUsing(
                        fn (ServiceRequestFeedback $record) => $record->serviceRequest->respondent->{$record->serviceRequest->respondent::displayNameKey()}
                    ),
                TextColumn::make('serviceRequest.assignedTo.user.name')
                    ->label('Assigned To')
                    ->searchable()
                    ->badge(fn (ServiceRequestFeedback $record) => is_null($record->serviceRequest->assignedTo))
                    ->default('Unassigned'),
                IconColumn::make('response_sla_compliance')
                    ->label('SLA Response')
                    ->state(
                        fn (ServiceRequestFeedback $record): ?SlaComplianceStatus => $record->serviceRequest->getResponseSlaComplianceStatus()
                    ),
                IconColumn::make('resolution_sla_compliance')
                    ->label('SLA Resolution')
                    ->state(
                        fn (ServiceRequestFeedback $record): ?SlaComplianceStatus => $record->serviceRequest->getResolutionSlaComplianceStatus()
                    ),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('m-d-Y h:i a'),
            ])->paginated([5, 10, 15, 20, 25]);
    }
}
