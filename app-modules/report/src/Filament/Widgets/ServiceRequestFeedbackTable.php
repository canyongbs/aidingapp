<?php

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
                    ->dateTime('M d, Y H:i'),
            ])->paginated([5, 10, 15, 20, 25]);
    }
}
