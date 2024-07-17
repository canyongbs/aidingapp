<?php

namespace AidingApp\Report\Filament\Widgets;

use Carbon\Carbon;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use AidingApp\Contact\Models\Organization;
use Filament\Widgets\TableWidget as BaseWidget;

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
                    return Organization::select('organizations.*')
                        ->selectRaw('COALESCE(SUM(sr.time_to_resolution), 0) / NULLIF(SUM(CASE WHEN sr.id IS NOT NULL THEN 1 END), 0) AS avg_time_to_resolution')
                        ->leftJoin('contacts AS c', 'organizations.id', '=', 'c.organization_id')
                        ->leftJoin('service_requests AS sr', 'c.id', '=', 'sr.respondent_id')
                        ->groupBy('organizations.id')
                        ->withCount(['contacts as contacts_service_requests_count' => function (Builder $query) {
                            $query->has('serviceRequests');
                        }])
                        ->orderBy('contacts_service_requests_count', 'desc');
                }
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Organization name'),
                TextColumn::make('contacts_service_requests_count')
                    ->label('Number of requests'),
                TextColumn::make('contacts_service_requests_avg_time_to_resolution')
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
