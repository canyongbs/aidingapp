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

use AidingApp\ServiceManagement\Enums\ServiceMonitoringStatus;
use AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

/**
 * Selects each target's `last_checked_at` and `current_status`:
 * - outage when its latest check failed
 * - degraded when its latest check succeeded, but another check failed within the last 24 hours
 * - operational when every check within the last 24 hours succeeded
 * - unknown when it has not been checked yet
 */
class WithCurrentStatus
{
    /**
     * @param Builder<ServiceMonitoringTarget> $query
     */
    public function __invoke(Builder $query): void
    {
        if ($query->getQuery()->columns === null) {
            $query->select($query->qualifyColumn('*'));
        }

        $query
            ->leftJoinLateral(
                HistoricalServiceMonitoring::query()
                    ->select(['historical_service_monitorings.succeeded', 'historical_service_monitorings.created_at'])
                    ->whereColumn('historical_service_monitorings.service_monitoring_target_id', 'service_monitoring_targets.id')
                    ->latest('historical_service_monitorings.created_at')
                    ->latest('historical_service_monitorings.id')
                    ->limit(1),
                'latest_check',
            )
            ->leftJoinLateral(
                HistoricalServiceMonitoring::query()
                    ->selectRaw('count(*) as failed_checks_count')
                    ->whereColumn('historical_service_monitorings.service_monitoring_target_id', 'service_monitoring_targets.id')
                    ->where('historical_service_monitorings.succeeded', false)
                    ->where('historical_service_monitorings.created_at', '>=', now()->subDay()),
                'recent_checks',
            )
            ->addSelect([
                'latest_check.created_at as last_checked_at',
                new Expression(static::statusExpression() . ' as current_status'),
            ])
            ->withCasts([
                'last_checked_at' => 'datetime',
                'current_status' => ServiceMonitoringStatus::class,
            ]);
    }

    /**
     * The SQL expression resolving to the `ServiceMonitoringStatus` value, for reuse when ordering (PostgreSQL
     * does not allow a selected alias to be referenced within an `order by` expression).
     */
    public static function statusExpression(): string
    {
        $unknown = ServiceMonitoringStatus::Unknown->value;
        $outage = ServiceMonitoringStatus::Outage->value;
        $degraded = ServiceMonitoringStatus::Degraded->value;
        $operational = ServiceMonitoringStatus::Operational->value;

        return <<<SQL
            (case
                when latest_check.succeeded is null then '{$unknown}'
                when not latest_check.succeeded then '{$outage}'
                when recent_checks.failed_checks_count > 0 then '{$degraded}'
                else '{$operational}'
            end)
            SQL;
    }
}
