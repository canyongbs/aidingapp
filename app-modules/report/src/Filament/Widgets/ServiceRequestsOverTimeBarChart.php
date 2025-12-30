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

use AidingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ServiceRequestsOverTimeBarChart extends BarChartReportWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Requests Over Time';

    protected int | string | array $columnSpan = 'full';

    public function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'min' => 0,
                ],
            ],
        ];
    }

    public function getData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $shouldBypassCache = filled($startDate) || filled($endDate);

        $serviceRequestTotalPerMonth = $shouldBypassCache
            ? $this->getServiceRequestsOverTimeData($startDate, $endDate)
            : Cache::tags(["{{$this->cacheTag}}"])->remember('service-requests-over-time-bar-chart', now()->addHours(24), function (): array {
                return $this->getServiceRequestsOverTimeData();
            });

        return [
            'datasets' => [
                [
                    'label' => 'Service requests',
                    'data' => array_values($serviceRequestTotalPerMonth),
                ],
            ],
            'labels' => array_keys($serviceRequestTotalPerMonth),
        ];
    }

    /**
     * @return array<string, int>
     */
    private function getServiceRequestsOverTimeData(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $serviceRequestTotalPerMonthData = ServiceRequest::query()
            ->toBase()
            ->selectRaw('date_trunc(\'month\', created_at) as month')
            ->selectRaw('COUNT(*) as total')
            ->when(
                $startDate && $endDate,
                fn ($query) => $query->whereBetween('created_at', [$startDate, $endDate]),
                fn ($query) => $query->where('created_at', '>', now()->subYearNoOverflow())
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $serviceRequestTotalPerMonthArray = [];

        $monthRange = ($startDate && $endDate)
            ? $this->getMonthRangeFromDates($startDate, $endDate)
            : range(11, 0);

        foreach ($monthRange as $monthOffset) {
            $month = ($startDate && $endDate)
                ? $startDate->copy()->addMonthsNoOverflow($monthOffset)
                : Carbon::now()->subMonthsNoOverflow($monthOffset);

            $serviceRequestTotalPerMonthArray[$month->format('M Y')] = $serviceRequestTotalPerMonthData[$month->startOfMonth()->toDateTimeString()] ?? 0;
        }

        return $serviceRequestTotalPerMonthArray;
    }

    /**
     * @return array<int>
     */
    private function getMonthRangeFromDates(?Carbon $startDate, ?Carbon $endDate): array
    {
        $start = $startDate->copy()->startOfMonth();
        $end = $endDate->copy()->endOfMonth();
        $months = [];

        $current = $start->copy();
        $monthIndex = 0;

        while ($current->lte($end)) {
            $months[] = $monthIndex;
            $current->addMonth();
            $monthIndex++;
        }

        return $months;
    }
}
