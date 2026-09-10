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

use AidingApp\Report\Filament\Widgets\Concerns\InteractsWithSlaReportData;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SlaComplianceOverTimeLineChart extends ChartReportWidget
{
    use InteractsWithSlaReportData;

    protected ?string $heading = 'SLA Compliance Over Time (Rolling 12 Months)';

    protected ?string $maxHeight = '250px';

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
                'y' => [
                    'display' => true,
                    'beginAtZero' => true,
                    'min' => 0,
                    'max' => 100,
                    'ticks' => [
                        'stepSize' => 20,
                        'suffix' => '%',
                    ],
                    'grid' => [
                        'display' => true,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    public function getData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $types = $this->getServiceRequestTypes();
        $classification = $this->getClassification();
        $assignedAgents = $this->getAssignedAgents();

        $shouldBypassCache = filled($startDate) || filled($endDate) || filled($types) || filled($classification) || filled($assignedAgents);

        $compliance = $shouldBypassCache
            ? $this->getComplianceOverTimeData()
            : Cache::tags(["{{$this->cacheTag}}"])->remember('sla-compliance-over-time', now()->addHours(24), fn (): array => $this->getComplianceOverTimeData());

        return [
            'datasets' => [
                [
                    'label' => 'Response SLA',
                    'data' => array_values($compliance['response']),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#3b82f6',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Resolution SLA',
                    'data' => array_values($compliance['resolution']),
                    'borderColor' => '#f97316',
                    'backgroundColor' => '#f97316',
                    'tension' => 0.3,
                ],
            ],
            'labels' => array_keys($compliance['response']),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array{response: array<string, float>, resolution: array<string, float>}
     */
    private function getComplianceOverTimeData(): array
    {
        $windowStart = now()->subMonthsNoOverflow(11)->startOfMonth();

        $serviceRequests = $this->applySlaFilters(ServiceRequest::query())
            ->where('created_at', '>=', $windowStart)
            ->with($this->slaEagerLoads())
            ->get()
            ->groupBy(fn (ServiceRequest $serviceRequest): string => $serviceRequest->created_at->format('Y-m'));

        $response = [];
        $resolution = [];

        foreach (range(11, 0) as $monthsAgo) {
            $month = Carbon::now()->subMonthsNoOverflow($monthsAgo);
            $label = $month->format('M Y');

            $metrics = $this->summarizeSlaMetrics(
                $serviceRequests->get($month->format('Y-m')) ?? collect()
            );

            $response[$label] = $metrics['response_compliance_percentage'];
            $resolution[$label] = $metrics['resolution_compliance_percentage'];
        }

        return [
            'response' => $response,
            'resolution' => $resolution,
        ];
    }
}
