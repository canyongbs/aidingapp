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

use AidingApp\ServiceManagement\Enums\ServiceMonitoringReportFrequency;
use AidingApp\ServiceManagement\Jobs\ServiceMonitoringReportJob;
use AidingApp\ServiceManagement\Jobs\ServiceMonitoringReportNotifyJob;
use AidingApp\ServiceManagement\Models\ServiceMonitoringReportConfiguration;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Features\ServiceMonitoringReportConfigurationsFeature;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceMonitoring = true;
    $settings->save();
});

it('successfully dispatches ServiceMonitoringReportNotifyJob', function (ServiceMonitoringReportFrequency $frequency) {
    Queue::fake();

    $numConfigurations = rand(1, 10);

    ServiceMonitoringReportConfiguration::factory()->count($numConfigurations)->active()->create([
        'frequency' => $frequency,
    ]);

    (new ServiceMonitoringReportJob($frequency))->handle();

    Queue::assertPushed(ServiceMonitoringReportNotifyJob::class, $numConfigurations);
})
    ->with(
        [
            fn () => ServiceMonitoringReportFrequency::Daily,
            fn () => ServiceMonitoringReportFrequency::Weekly,
            fn () => ServiceMonitoringReportFrequency::Monthly,
        ]
    );

it('does not dispatch when serviceMonitoring addon is disabled', function (ServiceMonitoringReportFrequency $frequency) {
    Queue::fake();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceMonitoring = false;
    $settings->save();

    ServiceMonitoringReportConfiguration::factory()->count(3)->active()->create([
        'frequency' => $frequency,
    ]);

    (new ServiceMonitoringReportJob($frequency))->handle();

    Queue::assertNotPushed(ServiceMonitoringReportNotifyJob::class);
})
    ->with(
        [
            fn () => ServiceMonitoringReportFrequency::Daily,
            fn () => ServiceMonitoringReportFrequency::Weekly,
            fn () => ServiceMonitoringReportFrequency::Monthly,
        ]
    );

it('only dispatches for configurations matching the specified report frequency', function (ServiceMonitoringReportFrequency $frequency) {
    Queue::fake();

    foreach (ServiceMonitoringReportFrequency::cases() as $case) {
        ServiceMonitoringReportConfiguration::factory()->active()->create([
            'frequency' => $case,
        ]);
    }

    (new ServiceMonitoringReportJob($frequency))->handle();

    Queue::assertPushed(ServiceMonitoringReportNotifyJob::class, 1);
})
    ->with(
        [
            fn () => ServiceMonitoringReportFrequency::Daily,
            fn () => ServiceMonitoringReportFrequency::Weekly,
            fn () => ServiceMonitoringReportFrequency::Monthly,
        ]
    );

it('does not dispatch for configurations that are inactive', function (ServiceMonitoringReportFrequency $frequency) {
    Queue::fake();

    ServiceMonitoringReportConfiguration::factory()->count(3)->inactive()->create([
        'frequency' => $frequency,
    ]);

    (new ServiceMonitoringReportJob($frequency))->handle();

    Queue::assertNotPushed(ServiceMonitoringReportNotifyJob::class);
})
    ->with(
        [
            fn () => ServiceMonitoringReportFrequency::Daily,
            fn () => ServiceMonitoringReportFrequency::Weekly,
            fn () => ServiceMonitoringReportFrequency::Monthly,
        ]
    );

it('still dispatches for confidential targets, since delivery suppression happens per recipient', function () {
    Queue::fake();

    $target = ServiceMonitoringTarget::factory()->confidential()->create();
    ServiceMonitoringReportConfiguration::factory()->active()->for($target, 'serviceMonitoringTarget')->create([
        'frequency' => ServiceMonitoringReportFrequency::Daily,
    ]);

    (new ServiceMonitoringReportJob(ServiceMonitoringReportFrequency::Daily))->handle();

    Queue::assertPushed(ServiceMonitoringReportNotifyJob::class, 1);
});

it('does not dispatch a configuration whose target has been soft-deleted', function () {
    Queue::fake();

    $target = ServiceMonitoringTarget::factory()->create();
    ServiceMonitoringReportConfiguration::factory()->active()->for($target, 'serviceMonitoringTarget')->create([
        'frequency' => ServiceMonitoringReportFrequency::Daily,
    ]);

    $target->delete();

    (new ServiceMonitoringReportJob(ServiceMonitoringReportFrequency::Daily))->handle();

    Queue::assertNotPushed(ServiceMonitoringReportNotifyJob::class);
});

// The following tests cover the pre-migration path, kept only until ServiceMonitoringReportConfigurationsFeature is cleaned up
it('falls back to legacy targets when the feature is inactive', function (ServiceMonitoringReportFrequency $frequency) {
    ServiceMonitoringReportConfigurationsFeature::deactivate();
    Queue::fake();

    $numTargets = rand(1, 10);

    ServiceMonitoringTarget::factory()->count($numTargets)->create([
        'report_frequency' => $frequency,
        'is_reporting_active' => true,
    ]);

    (new ServiceMonitoringReportJob($frequency))->handle();

    Queue::assertPushed(ServiceMonitoringReportNotifyJob::class, $numTargets);
})
    ->with(
        [
            fn () => ServiceMonitoringReportFrequency::Daily,
            fn () => ServiceMonitoringReportFrequency::Weekly,
            fn () => ServiceMonitoringReportFrequency::Monthly,
        ]
    );

it('does not dispatch for legacy targets with inactive reporting when the feature is inactive', function () {
    ServiceMonitoringReportConfigurationsFeature::deactivate();
    Queue::fake();

    ServiceMonitoringTarget::factory()->count(3)->create([
        'report_frequency' => ServiceMonitoringReportFrequency::Daily,
        'is_reporting_active' => false,
    ]);

    (new ServiceMonitoringReportJob(ServiceMonitoringReportFrequency::Daily))->handle();

    Queue::assertNotPushed(ServiceMonitoringReportNotifyJob::class);
});
