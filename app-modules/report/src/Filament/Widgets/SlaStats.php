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
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class SlaStats extends StatsOverviewReportWidget
{
    use InteractsWithSlaReportData;

    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 4,
        'lg' => 4,
    ];

    public function getStats(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $types = $this->getServiceRequestTypes();
        $classification = $this->getClassification();
        $assignedAgents = $this->getAssignedAgents();

        $shouldBypassCache = filled($startDate) || filled($endDate) || filled($types) || filled($classification) || filled($assignedAgents);

        $metrics = $shouldBypassCache
            ? $this->calculateSlaStats()
            : Cache::tags(["{{$this->cacheTag}}"])->remember('sla-stats', now()->addHours(24), fn (): array => $this->calculateSlaStats());

        return [
            Stat::make('Total Requests', number_format($metrics['total'])),
            Stat::make('SLA Breaches', number_format($metrics['breaches']))
                ->color($metrics['breaches'] > 0 ? 'danger' : 'success'),
            Stat::make('Response Breaches', "{$metrics['response_breach_percentage']}%")
                ->color($metrics['response_breach_percentage'] > 0 ? 'danger' : 'success'),
            Stat::make('Resolution Breaches', "{$metrics['resolution_breach_percentage']}%")
                ->color($metrics['resolution_breach_percentage'] > 0 ? 'danger' : 'success'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function calculateSlaStats(): array
    {
        return $this->summarizeSlaMetrics(
            $this->slaServiceRequestsQuery()->with($this->slaEagerLoads())->get()
        );
    }
}
