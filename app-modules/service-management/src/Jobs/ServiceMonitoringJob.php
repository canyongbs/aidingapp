<?php

namespace AidingApp\ServiceManagement\Jobs;

use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ServiceMonitoringJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public ServiceMonitoringFrequency $interval)
    {}

    public function handle(): void
    {
        ServiceMonitoringTarget::where('frequency', $this->interval)
            ->chunkById(100, function (Collection $serviceMonitoringTargets) {
                foreach($serviceMonitoringTargets as $serviceMonitoringTarget) {
                    $this->batch()->add(new ServiceMonitoringCheckJob($serviceMonitoringTarget));
                }
            });
    }
}
