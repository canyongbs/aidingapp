<?php

namespace App\Jobs;

use AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class ServiceMonitoringJob implements ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(public ServiceMonitoringTarget $serviceMonitoringTarget)
    {
    }

    public function handle(): void
    {        
        try {
            $response = Http::get($this->serviceMonitoringTarget->domain);
        } catch (Exception $e) {
            // ???
        }

        new HistoricalServiceMonitoring([
            'response' => $response->status(),
            'service_monitoring_target_id' => $this->serviceMonitoringTarget->id,
        ]);

        if($response->status() !== 200) {
            // mail event
        }
    }
}
