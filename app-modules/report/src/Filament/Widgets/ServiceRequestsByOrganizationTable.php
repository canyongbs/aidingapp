<?php

namespace AidingApp\Report\Filament\Widgets;

use Carbon\Carbon;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Filament\Tables\Grouping\Group;

class ServiceRequestsByOrganizationTable extends BaseWidget
{
    public string $cacheTag;

    protected static ?string $heading = 'Service Requests by Organization';

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
                    return ServiceRequest::whereHas('respondent.organizations')
                        ->with(['respondent.organizations']);
                }
            )
            ->columns([
                TextColumn::make('respondent.organizations.name')
                    ->label('Organization name'),
                TextColumn::make('title')
                    ->label('Service request name'),
                TextColumn::make('priority.type.name')
                    ->label('Service request type'),
                TextColumn::make('time_to_resolution')
                    ->formatStateUsing(function ($state) {
                        $interval = Carbon::now()->diffAsCarbonInterval(Carbon::now()->addSeconds($state));
                        $days = $interval->d;
                        $hours = $interval->h;
                        $minutes = $interval->i;

                        return "{$days}d {$hours}h {$minutes}m";
                    })
                    ->label('Resolution time'),
            ])
            ->groups([
                Group::make('respondent.organizations.name')
                    ->label('Organization')
                    ->collapsible(),
            ])
            ->paginated([10]);
    }
}
