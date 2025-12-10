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
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ServiceRequestStatusDistributionDonutChart extends ChartReportWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Request Status Distribution';

    protected static ?string $maxHeight = '200px';

    protected int | string | array $columnSpan = 'full';

    public function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }

    public function getData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $shouldBypassCache = filled($startDate) || filled($endDate);

        $serviceRequestByStatus = $shouldBypassCache
            ? $this->getServiceRequestStatusData($startDate, $endDate)
            : Cache::tags(["{{$this->cacheTag}}"])->remember('service-request-status-distribution', now()->addHours(24), function (): Collection {
                return $this->getServiceRequestStatusData();
            });

        return [
            'labels' => $serviceRequestByStatus->pluck('name'),
            'datasets' => [
                [
                    'label' => 'My First Dataset',
                    'data' => $serviceRequestByStatus->pluck('service_requests_count'),
                    'backgroundColor' => $serviceRequestByStatus->pluck('bg_color'),
                    'hoverOffset' => 4,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * @return Collection<int, ServiceRequestStatus>
     */
    private function getServiceRequestStatusData(?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $serviceRequestByStatusData = ServiceRequestStatus::withCount([
            'serviceRequests' => function (Builder $query) use ($startDate, $endDate) {
                $query->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                );
            },
        ])->get(['id', 'name']);

        return $serviceRequestByStatusData->map(function (ServiceRequestStatus $status) {
            $status['bg_color'] = $status->color->getRgb();

            return $status;
        });
    }
}
