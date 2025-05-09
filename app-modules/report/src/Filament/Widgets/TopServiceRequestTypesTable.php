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

use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Livewire\Attributes\On;

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
                        $interval = Carbon::now()->diffAsCarbonInterval(Carbon::now()->addSeconds((float) $state));
                        $days = $interval->d;
                        $hours = $interval->h;
                        $minutes = $interval->i;

                        return "{$days}d {$hours}h {$minutes}m";
                    })
                    ->label('Average resolution time'),
            ])
            ->paginated([5]);
    }
}
