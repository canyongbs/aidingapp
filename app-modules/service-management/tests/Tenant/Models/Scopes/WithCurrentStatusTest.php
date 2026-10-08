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

use AidingApp\ServiceManagement\Enums\ServiceMonitoringStatus;
use AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring;
use AidingApp\ServiceManagement\Models\Scopes\WithCurrentStatus;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;

function findWithCurrentStatus(ServiceMonitoringTarget $serviceMonitoringTarget): ServiceMonitoringTarget
{
    return ServiceMonitoringTarget::query()
        ->tap(new WithCurrentStatus())
        ->findOrFail($serviceMonitoringTarget->getKey());
}

it('resolves the current status from the latest check and the checks within the last 24 hours', function (array $checks, ServiceMonitoringStatus $status) {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    foreach ($checks as $hoursAgo => $succeeded) {
        HistoricalServiceMonitoring::factory()
            ->for($serviceMonitoringTarget)
            ->state(['succeeded' => $succeeded, 'created_at' => now()->subHours($hoursAgo)])
            ->create();
    }

    expect(findWithCurrentStatus($serviceMonitoringTarget)->getCurrentStatus())->toBe($status);
})->with([
    'no checks' => [[], ServiceMonitoringStatus::Unknown],
    'every check succeeded' => [[3 => true, 2 => true, 1 => true], ServiceMonitoringStatus::Operational],
    'latest check failed' => [[3 => true, 2 => true, 1 => false], ServiceMonitoringStatus::Outage],
    'earlier check failed within 24 hours' => [[3 => true, 2 => false, 1 => true], ServiceMonitoringStatus::Degraded],
    'earlier check failed over 24 hours ago' => [[25 => false, 2 => true, 1 => true], ServiceMonitoringStatus::Operational],
]);

it('selects when the target was last checked', function () {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => now()->subHours(2)]);
    $latestCheck = HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => now()->subMinutes(5)]);

    expect(findWithCurrentStatus($serviceMonitoringTarget)->getLastCheckedAt()->toDateTimeString())
        ->toBe($latestCheck->created_at->toDateTimeString());
});

it('does not consider the checks of other targets', function () {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();
    $check = HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => now()->subHours(2)]);

    HistoricalServiceMonitoring::factory()->failed()->create(['created_at' => now()->subHour()]);

    $serviceMonitoringTarget = findWithCurrentStatus($serviceMonitoringTarget);

    expect($serviceMonitoringTarget->getCurrentStatus())->toBe(ServiceMonitoringStatus::Operational)
        ->and($serviceMonitoringTarget->getLastCheckedAt()->toDateTimeString())->toBe($check->created_at->toDateTimeString());
});
