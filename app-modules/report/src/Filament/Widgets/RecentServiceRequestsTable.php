<?php

namespace AidingApp\Report\Filament\Widgets;

use Carbon\Carbon;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use AidingApp\ServiceManagement\Models\ServiceRequest;

class RecentServiceRequestsTable extends BaseWidget
{
    public string $cacheTag;

    protected static ?string $heading = 'Recent Service Requests';

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 4,
        'lg' => 4,
    ];

    public function mount(string $cacheTag)
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget()
    {
        $this->dispatch('$refresh');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                function () {
                    return ServiceRequest::with([
                        'latestInboundServiceRequestUpdate',
                        'latestOutboundServiceRequestUpdate',
                        'priority' => [
                            'sla',
                        ],
                        'status',
                    ])
                        ->where('created_at', '>=', Carbon::now()->subDays(30))
                        ->orderBy('created_at', 'desc');
                }
            )
            ->columns([
                TextColumn::make('service_request_number')
                    ->label('Service Request #'),
                TextColumn::make('title'),
                TextColumn::make('status.name'),
                TextColumn::make('priority.name'),
                TextColumn::make('priority.type.name'),
                TextColumn::make('assignedTo.user.name')
                    ->label('Assigned to'),
            ])
            ->paginated([10]);
    }
}
