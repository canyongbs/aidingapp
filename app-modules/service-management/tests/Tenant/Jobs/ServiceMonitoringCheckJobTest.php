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

use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Jobs\ServiceMonitoringCheckJob;
use AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Notifications\ServiceMonitoringNotification;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertDatabaseHas;

it('sends a notification if the response is not 200', function ($frequency) {
    Http::fake(function () {
        return Http::response('Test', 500);
    });
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create(['frequency' => $frequency, 'is_notified_via_email' => true]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo(
        $user,
        ServiceMonitoringNotification::class
    );

    assertDatabaseHas(HistoricalServiceMonitoring::class, ['response' => 500, 'succeeded' => false, 'service_monitoring_target_id' => $serviceMonitorTarget->getKey()]);
})
    ->with(
        [
            fn () => ServiceMonitoringFrequency::OneHour,
            fn () => ServiceMonitoringFrequency::TwentyFourHours,
        ]
    );

it('does not send a notification if the response is 200', function ($frequency) {
    Http::fake(function () {
        return Http::response('Test', 200);
    });
    Notification::fake();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached(User::factory()->create())
        ->create(['frequency' => $frequency]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertNothingSent();

    assertDatabaseHas(HistoricalServiceMonitoring::class, ['response' => 200, 'succeeded' => true, 'service_monitoring_target_id' => $serviceMonitorTarget->getKey()]);
})
    ->with(
        [
            fn () => ServiceMonitoringFrequency::OneHour,
            fn () => ServiceMonitoringFrequency::TwentyFourHours,
        ]
    );

it('handles unresolvable host errors gracefully', function ($frequency) {
    Http::fake(function () {
        throw new ConnectionException('Could not resolve host');
    });
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create(['frequency' => $frequency, 'is_notified_via_email' => true]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo(
        $user,
        ServiceMonitoringNotification::class
    );

    assertDatabaseHas(HistoricalServiceMonitoring::class, ['response' => 523, 'succeeded' => false, 'service_monitoring_target_id' => $serviceMonitorTarget->getKey()]);
})
    ->with(
        [
            fn () => ServiceMonitoringFrequency::OneHour,
            fn () => ServiceMonitoringFrequency::TwentyFourHours,
        ]
    );
