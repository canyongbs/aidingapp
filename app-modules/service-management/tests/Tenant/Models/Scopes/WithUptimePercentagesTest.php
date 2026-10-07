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

use AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring;
use AidingApp\ServiceManagement\Models\Scopes\WithUptimePercentages;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;

function findWithUptimePercentages(ServiceMonitoringTarget $serviceMonitoringTarget): ServiceMonitoringTarget
{
    return ServiceMonitoringTarget::query()
        ->tap(new WithUptimePercentages(['one_day_uptime' => 1, 'seven_day_uptime' => 7]))
        ->findOrFail($serviceMonitoringTarget->getKey());
}

it('selects the percentage of successful checks within each period', function () {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->failed()->create(['created_at' => now()->subDays(6)->subHour()]);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->failed()->create(['created_at' => now()->subHours(20)]);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => now()->subHours(10)]);
    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => now()->subHour()]);

    $serviceMonitoringTarget = findWithUptimePercentages($serviceMonitoringTarget);

    expect($serviceMonitoringTarget->getSelectedUptimePercentage('one_day_uptime'))->toBe(66.67)
        ->and($serviceMonitoringTarget->getSelectedUptimePercentage('seven_day_uptime'))->toBe(50.0);
});

it('selects `null` when the target has not been checked for most of the period', function () {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    HistoricalServiceMonitoring::factory()->for($serviceMonitoringTarget)->create(['created_at' => now()->subDays(3)]);

    $serviceMonitoringTarget = findWithUptimePercentages($serviceMonitoringTarget);

    expect($serviceMonitoringTarget->getSelectedUptimePercentage('one_day_uptime'))->toBeNull()
        ->and($serviceMonitoringTarget->getSelectedUptimePercentage('seven_day_uptime'))->toBeNull();
});

it('selects `null` when the target has never been checked', function () {
    $serviceMonitoringTarget = findWithUptimePercentages(ServiceMonitoringTarget::factory()->create());

    expect($serviceMonitoringTarget->getSelectedUptimePercentage('one_day_uptime'))->toBeNull()
        ->and($serviceMonitoringTarget->getSelectedUptimePercentage('seven_day_uptime'))->toBeNull();
});
