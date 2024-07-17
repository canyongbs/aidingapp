<?php

namespace AidingApp\Report\Filament\Widgets;

use Carbon\Carbon;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

class TopServiceRequestTypesTable extends BaseWidget
{
    public string $cacheTag;

    protected static ?string $heading = 'Top Request Types';

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
                    return ServiceRequestType::withCount('serviceRequests')
                        ->withAvg('serviceRequests', 'time_to_resolution')
                        ->orderBy('service_requests_count', 'desc');
                }
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Type'),
                TextColumn::make('service_requests_count')
                    ->label('Count'),
                TextColumn::make('service_requests_avg_time_to_resolution')
                    ->formatStateUsing(function ($state) {
                        $interval = Carbon::now()->diffAsCarbonInterval(Carbon::now()->addSeconds($state));
                        $days = $interval->d;
                        $hours = $interval->h;
                        $minutes = $interval->i;

                        return "{$days}d {$hours}h {$minutes}m";
                    })
                    ->label('Average resolution time'),
            ])
            ->paginated([10]);
    }
}
