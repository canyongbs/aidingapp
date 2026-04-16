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

use AidingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use AidingApp\ServiceManagement\Enums\ServiceRequestCategory;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\Cache;

class ServiceRequestCategoryDistributionDonutChart extends ChartReportWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Request Category Distribution';

    protected ?string $maxHeight = '200px';

    protected int | string | array $columnSpan = 2;

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

        $serviceRequestByCategory = $shouldBypassCache
            ? $this->getServiceRequestCategoryData($startDate, $endDate)
            : Cache::tags(["{{$this->cacheTag}}"])->remember('service-request-category-distribution', now()->addHours(24), function (): Collection {
                return $this->getServiceRequestCategoryData();
            });

        return [
            'labels' => $serviceRequestByCategory->pluck('label'),
            'datasets' => [
                [
                    'label' => 'Service Requests by Category',
                    'data' => $serviceRequestByCategory->pluck('count'),
                    'backgroundColor' => $serviceRequestByCategory->pluck('bg_color'),
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
     * @return BaseCollection<int, array{label: string, count: int, bg_color: string}>
     */
    private function getServiceRequestCategoryData(?Carbon $startDate = null, ?Carbon $endDate = null): BaseCollection
    {
        $counts = [];

        foreach (ServiceRequestCategory::cases() as $case) {
            $query = ServiceRequest::where('issue_category', $case->value);

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            $count = $query->count();

            // Only include non-zero counts in the chart
            if ($count > 0) {
                $counts[] = [
                    'label' => (string) $case->getLabel(),
                    'count' => $count,
                    'bg_color' => $this->colorToRgb($case->getColor()),
                ];
            }
        }

        return collect($counts);
    }

    /**
     * Convert Filament color name to RGB format
     */
    private function colorToRgb(string $color): string
    {
        $colorMap = [
            'danger' => 'rgb(239, 68, 68)',     // red-500
            'primary' => 'rgb(59, 130, 246)',   // blue-500
            'success' => 'rgb(34, 197, 94)',    // green-500
            'warning' => 'rgb(245, 158, 11)',   // amber-500
            'info' => 'rgb(14, 165, 233)',      // sky-500
            'gray' => 'rgb(107, 114, 128)',     // gray-500
        ];

        return $colorMap[$color] ?? 'rgb(59, 130, 246)';
    }
}
