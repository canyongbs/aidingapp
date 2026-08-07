<?php

use AidingApp\ServiceManagement\Enums\ServiceMonitoringReportFrequency;
use AidingApp\ServiceManagement\Jobs\ServiceMonitoringReportJob;
use AidingApp\ServiceManagement\Jobs\ServiceMonitoringReportNotifyJob;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Queue;

it('successfully dispatches ServiceMonitoringReportNotifyJob', function ($frequency) {
    Queue::fake();

    $numTargets = rand(1, 10);

    ServiceMonitoringTarget::factory()->count($numTargets)->create(['report_frequency' => $frequency]);

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

it('does not dispatch when serviceMonitoring addon is disabled', function ($frequency) {
    Queue::fake();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceMonitoring = false;
    $settings->save();

    ServiceMonitoringTarget::factory()->count(3)->create(['report_frequency' => $frequency]);

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

it('only dispatches for targets matching the specified report frequency', function ($frequency) {
    Queue::fake();

    foreach (ServiceMonitoringReportFrequency::cases() as $case) {
        ServiceMonitoringTarget::factory()->create(['report_frequency' => $case]);
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
