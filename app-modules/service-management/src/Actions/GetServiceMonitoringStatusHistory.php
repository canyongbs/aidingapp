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

namespace AidingApp\ServiceManagement\Actions;

use AidingApp\ServiceManagement\DataTransferObjects\ServiceMonitoringHistoryBucketData;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringHistoryPeriod;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringStatus;
use AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring;
use Carbon\CarbonImmutable;
use DateTimeZone;
use Exception;
use Illuminate\Support\Collection;
use IntlTimeZone;

class GetServiceMonitoringStatusHistory
{
    /**
     * Groups the checks of each target into consecutive buckets covering the period, oldest first. Buckets are
     * aligned to the given timezone, so that a "day" is a calendar day for whoever is viewing the history.
     *
     * @param array<int, string> $serviceMonitoringTargetIds
     *
     * @return array<string, array<int, ServiceMonitoringHistoryBucketData>> The buckets, keyed by target ID.
     */
    public function __invoke(array $serviceMonitoringTargetIds, ServiceMonitoringHistoryPeriod $period, string $timezone): array
    {
        if (blank($serviceMonitoringTargetIds)) {
            return [];
        }

        $timezone = $this->resolveTimezone($timezone);

        $databaseTimezone = config('app.timezone');
        assert(is_string($databaseTimezone));

        $firstBucketStartsAt = $period->getFirstBucketStartsAt(CarbonImmutable::now($timezone));

        // Days are keyed by their local calendar date, but hours and minutes are keyed by the instant they start
        // at, as keying them by local time would merge the hour that repeats when daylight saving time ends.
        $isKeyedByLocalTime = $period->getBucketUnit() === 'day';

        $localCreatedAt = 'historical_service_monitorings.created_at at time zone ? at time zone ?';

        $bucketCounts = HistoricalServiceMonitoring::query()
            ->select('historical_service_monitorings.service_monitoring_target_id')
            ->selectRaw(
                $isKeyedByLocalTime
                    ? "to_char(date_trunc(?, {$localCreatedAt}), 'YYYY-MM-DD HH24:MI:SS') as bucket"
                    : "to_char(date_trunc(?, {$localCreatedAt}) - ({$localCreatedAt} - historical_service_monitorings.created_at), 'YYYY-MM-DD HH24:MI:SS') as bucket",
                $isKeyedByLocalTime
                    ? [$period->getBucketUnit(), $databaseTimezone, $timezone]
                    : [$period->getBucketUnit(), $databaseTimezone, $timezone, $databaseTimezone, $timezone],
            )
            ->selectRaw('count(*) as checks_count')
            ->selectRaw('count(*) filter (where historical_service_monitorings.succeeded) as successful_checks_count')
            ->whereIn('historical_service_monitorings.service_monitoring_target_id', $serviceMonitoringTargetIds)
            ->where('historical_service_monitorings.created_at', '>=', $firstBucketStartsAt->setTimezone($databaseTimezone))
            ->groupBy('historical_service_monitorings.service_monitoring_target_id', 'bucket')
            ->toBase()
            ->get()
            ->groupBy('service_monitoring_target_id')
            ->map(fn (Collection $targetBucketCounts): Collection => $targetBucketCounts->keyBy('bucket'));

        $history = [];

        foreach ($serviceMonitoringTargetIds as $serviceMonitoringTargetId) {
            $history[$serviceMonitoringTargetId] = [];

            for ($index = 0; $index < $period->getBucketCount(); $index++) {
                $bucketStartsAt = $firstBucketStartsAt->addUnit($period->getBucketUnit(), $index);

                $bucketKey = ($isKeyedByLocalTime ? $bucketStartsAt : $bucketStartsAt->setTimezone($databaseTimezone))->format('Y-m-d H:i:s');

                $counts = $bucketCounts->get($serviceMonitoringTargetId)?->get($bucketKey);

                $checksCount = (int) ($counts->checks_count ?? 0);
                $successfulChecksCount = (int) ($counts->successful_checks_count ?? 0);

                $history[$serviceMonitoringTargetId][] = new ServiceMonitoringHistoryBucketData(
                    startsAt: $bucketStartsAt->toIso8601String(),
                    status: ServiceMonitoringStatus::fromCheckCounts($checksCount, $successfulChecksCount),
                    uptimePercentage: $checksCount > 0 ? round(($successfulChecksCount / $checksCount) * 100, 2) : null,
                    checksCount: $checksCount,
                    failedChecksCount: $checksCount - $successfulChecksCount,
                );
            }
        }

        return $history;
    }

    /**
     * Maps legacy timezone aliases (e.g. `Asia/Calcutta`) that PHP does not recognize to an equivalent zone, falling
     * back to the app's timezone.
     */
    protected function resolveTimezone(string $timezone): string
    {
        if ($this->isSupportedTimezone($timezone)) {
            return $timezone;
        }

        for ($index = 0; $index < IntlTimeZone::countEquivalentIDs($timezone); $index++) {
            $equivalentTimezone = IntlTimeZone::getEquivalentID($timezone, $index);

            // Abbreviations such as `IST` are skipped, as they are ambiguous and ignore daylight saving time.
            if (str_contains($equivalentTimezone, '/') && $this->isSupportedTimezone($equivalentTimezone)) {
                return $equivalentTimezone;
            }
        }

        $appTimezone = config('app.timezone');
        assert(is_string($appTimezone));

        return $appTimezone;
    }

    protected function isSupportedTimezone(string $timezone): bool
    {
        try {
            new DateTimeZone($timezone);

            return true;
        } catch (Exception) {
            return false;
        }
    }
}
