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

use AidingApp\ServiceManagement\Actions\GetServiceMonitoringStatusHistory;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringHistoryPeriod;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringStatus;
use AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Carbon\CarbonImmutable;

use function Pest\Laravel\travelTo;

beforeEach(function () {
    travelTo(CarbonImmutable::parse('2026-09-28 10:30:00', 'UTC'));
});

it('groups the checks into buckets covering the period', function (ServiceMonitoringHistoryPeriod $period, int $bucketCount, string $firstBucketStartsAt, string $lastBucketStartsAt) {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    $history = app(GetServiceMonitoringStatusHistory::class)([$serviceMonitoringTarget->getKey()], $period, 'UTC');

    expect($history[$serviceMonitoringTarget->getKey()])->toHaveCount($bucketCount)
        ->and($history[$serviceMonitoringTarget->getKey()][0]->startsAt)->toBe($firstBucketStartsAt)
        ->and($history[$serviceMonitoringTarget->getKey()][$bucketCount - 1]->startsAt)->toBe($lastBucketStartsAt);
})->with([
    'past hour' => [ServiceMonitoringHistoryPeriod::PastHour, 60, '2026-09-28T09:31:00+00:00', '2026-09-28T10:30:00+00:00'],
    'past day' => [ServiceMonitoringHistoryPeriod::PastDay, 24, '2026-09-27T11:00:00+00:00', '2026-09-28T10:00:00+00:00'],
    'past month' => [ServiceMonitoringHistoryPeriod::PastMonth, 30, '2026-08-30T00:00:00+00:00', '2026-09-28T00:00:00+00:00'],
]);

it('summarizes the checks within each bucket', function () {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => '2026-09-28 10:05:00']);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->failed()->create(['created_at' => '2026-09-28 10:20:00']);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->failed()->create(['created_at' => '2026-09-28 09:10:00']);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => '2026-09-28 08:10:00']);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->failed()->create(['created_at' => '2026-09-27 10:59:59']);

    $buckets = app(GetServiceMonitoringStatusHistory::class)([$serviceMonitoringTarget->getKey()], ServiceMonitoringHistoryPeriod::PastDay, 'UTC')[$serviceMonitoringTarget->getKey()];

    expect($buckets[23]->status)->toBe(ServiceMonitoringStatus::Degraded)
        ->and($buckets[23]->uptimePercentage)->toBe(50.0)
        ->and($buckets[23]->checksCount)->toBe(2)
        ->and($buckets[23]->failedChecksCount)->toBe(1)
        ->and($buckets[22]->status)->toBe(ServiceMonitoringStatus::Outage)
        ->and($buckets[22]->uptimePercentage)->toBe(0.0)
        ->and($buckets[21]->status)->toBe(ServiceMonitoringStatus::Operational)
        ->and($buckets[21]->uptimePercentage)->toBe(100.0)
        ->and($buckets[0]->status)->toBe(ServiceMonitoringStatus::Unknown)
        ->and($buckets[0]->uptimePercentage)->toBeNull()
        ->and(collect($buckets)->sum('checksCount'))->toBe(4);
});

it('aligns the buckets to the given timezone', function () {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    // 10:00 pm on the 27th in New York.
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->failed()->create(['created_at' => '2026-09-28 02:00:00']);

    $buckets = app(GetServiceMonitoringStatusHistory::class)([$serviceMonitoringTarget->getKey()], ServiceMonitoringHistoryPeriod::PastMonth, 'America/New_York')[$serviceMonitoringTarget->getKey()];

    expect($buckets[29]->startsAt)->toBe('2026-09-28T00:00:00-04:00')
        ->and($buckets[29]->status)->toBe(ServiceMonitoringStatus::Unknown)
        ->and($buckets[28]->startsAt)->toBe('2026-09-27T00:00:00-04:00')
        ->and($buckets[28]->status)->toBe(ServiceMonitoringStatus::Outage);
});

it('resolves a legacy timezone alias unsupported by the date extension to an equivalent zone', function (string $timezone, string $lastBucketStartsAt) {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    $buckets = app(GetServiceMonitoringStatusHistory::class)([$serviceMonitoringTarget->getKey()], ServiceMonitoringHistoryPeriod::PastMonth, $timezone)[$serviceMonitoringTarget->getKey()];

    expect($buckets[29]->startsAt)->toBe($lastBucketStartsAt);
})->with([
    'without daylight saving time' => ['Asia/Calcutta', '2026-09-28T00:00:00+05:30'],
    'observing daylight saving time' => ['US/Eastern', '2026-09-28T00:00:00-04:00'],
]);

it('keeps the hour repeated when daylight saving time ends as separate buckets', function () {
    travelTo(CarbonImmutable::parse('2026-11-01 08:30:00', 'UTC'));

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    // 1:30 am in New York, before and after the clocks go back.
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->failed()->create(['created_at' => '2026-11-01 05:30:00']);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => '2026-11-01 06:30:00']);

    $buckets = collect(app(GetServiceMonitoringStatusHistory::class)([$serviceMonitoringTarget->getKey()], ServiceMonitoringHistoryPeriod::PastDay, 'America/New_York')[$serviceMonitoringTarget->getKey()])
        ->keyBy('startsAt');

    expect($buckets['2026-11-01T01:00:00-04:00']->status)->toBe(ServiceMonitoringStatus::Outage)
        ->and($buckets['2026-11-01T01:00:00-04:00']->checksCount)->toBe(1)
        ->and($buckets['2026-11-01T01:00:00-05:00']->status)->toBe(ServiceMonitoringStatus::Operational)
        ->and($buckets['2026-11-01T01:00:00-05:00']->checksCount)->toBe(1);
});

it('skips the hour missing when daylight saving time starts', function () {
    travelTo(CarbonImmutable::parse('2026-03-08 09:30:00', 'UTC'));

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    // 1:30 am and 3:30 am in New York, either side of the clocks going forward.
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->failed()->create(['created_at' => '2026-03-08 06:30:00']);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => '2026-03-08 07:30:00']);

    $buckets = collect(app(GetServiceMonitoringStatusHistory::class)([$serviceMonitoringTarget->getKey()], ServiceMonitoringHistoryPeriod::PastDay, 'America/New_York')[$serviceMonitoringTarget->getKey()])
        ->keyBy('startsAt');

    expect($buckets)->toHaveCount(24)
        ->and($buckets->keys()->filter(fn (string $startsAt): bool => str_starts_with($startsAt, '2026-03-08T02:')))->toBeEmpty()
        ->and($buckets['2026-03-08T01:00:00-05:00']->status)->toBe(ServiceMonitoringStatus::Outage)
        ->and($buckets['2026-03-08T03:00:00-04:00']->status)->toBe(ServiceMonitoringStatus::Operational);
});

it('falls back to the app timezone when the timezone cannot be resolved at all', function () {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    $buckets = app(GetServiceMonitoringStatusHistory::class)([$serviceMonitoringTarget->getKey()], ServiceMonitoringHistoryPeriod::PastMonth, 'Not/AReal_Zone')[$serviceMonitoringTarget->getKey()];

    expect($buckets[29]->startsAt)->toBe('2026-09-28T00:00:00+00:00');
});

it('keeps the history of each target separate', function () {
    [$firstServiceMonitoringTarget, $secondServiceMonitoringTarget] = ServiceMonitoringTarget::factory()->count(2)->create();

    HistoricalServiceMonitoring::factory()->for($firstServiceMonitoringTarget)->failed()->create(['created_at' => '2026-09-28 10:05:00']);

    $history = app(GetServiceMonitoringStatusHistory::class)(
        [$firstServiceMonitoringTarget->getKey(), $secondServiceMonitoringTarget->getKey()],
        ServiceMonitoringHistoryPeriod::PastHour,
        'UTC',
    );

    expect($history[$firstServiceMonitoringTarget->getKey()][34]->status)->toBe(ServiceMonitoringStatus::Outage)
        ->and(collect($history[$secondServiceMonitoringTarget->getKey()])->pluck('status')->unique()->all())->toBe([ServiceMonitoringStatus::Unknown]);
});
