<?php

use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Jobs\ServiceMonitoringCheckJob;
use AidingApp\ServiceManagement\Jobs\ServiceMonitoringJob;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Illuminate\Support\Facades\Queue;

it('successfully dispatches', function () {
    Queue::fake();

    $numJobs = rand(1,10);

    ServiceMonitoringTarget::factory()->count($numJobs)->create(['frequency' => ServiceMonitoringFrequency::OneHour]);

    (new ServiceMonitoringJob(ServiceMonitoringFrequency::OneHour))->handle();

    Queue::assertPushed(ServiceMonitoringCheckJob::class, $numJobs);
});
