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

namespace AidingApp\ServiceManagement\Models\Scopes;

use AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Selects the percentage of successful checks (to two decimal places) over each of the given periods.
 *
 * Matches `ServiceMonitoringTarget::getUptimePercentage()`: the percentage is `null` when the target has not been
 * checked for (nearly) the whole period, as it would otherwise overstate how long the target has been up for.
 *
 * The selected aliases can be ordered by as `uptime_stats.<alias>`.
 */
class WithUptimePercentages
{
    /**
     * @param array<string, int> $periods The number of days in each period, keyed by the alias to select it as.
     */
    public function __construct(
        protected readonly array $periods,
    ) {}

    /**
     * @param Builder<ServiceMonitoringTarget> $query
     */
    public function __invoke(Builder $query): void
    {
        if ($query->getQuery()->columns === null) {
            $query->select($query->qualifyColumn('*'));
        }

        $stats = HistoricalServiceMonitoring::query()
            ->whereColumn('historical_service_monitorings.service_monitoring_target_id', 'service_monitoring_targets.id')
            ->where('historical_service_monitorings.created_at', '>=', now()->subDays(max($this->periods)));

        foreach ($this->periods as $alias => $days) {
            $periodStartsAt = now()->subDays($days);

            $stats->selectRaw(
                <<<SQL
                    case
                        when min(historical_service_monitorings.created_at) filter (where historical_service_monitorings.created_at >= ?) <= ?
                        then round(
                            100.0
                            * count(*) filter (where historical_service_monitorings.succeeded and historical_service_monitorings.created_at >= ?)
                            / count(*) filter (where historical_service_monitorings.created_at >= ?),
                            2
                        )
                    end as {$alias}
                    SQL,
                [$periodStartsAt, $periodStartsAt->copy()->addDay(), $periodStartsAt, $periodStartsAt],
            );
        }

        $query
            ->leftJoinLateral($stats, 'uptime_stats')
            ->addSelect(array_map(fn (string $alias): string => "uptime_stats.{$alias}", array_keys($this->periods)))
            ->withCasts(array_fill_keys(array_keys($this->periods), 'float'));
    }
}
