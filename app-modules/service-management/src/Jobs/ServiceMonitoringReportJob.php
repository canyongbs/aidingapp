<?php

namespace AidingApp\ServiceManagement\Jobs;

use AidingApp\ServiceManagement\Enums\ServiceMonitoringReportFrequency;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Settings\LicenseSettings;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ServiceMonitoringReportJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public ServiceMonitoringReportFrequency $frequency) {}

    public function uniqueId(): string
    {
        return $this->frequency->value;
    }

    /**
     * Return the period for which this job should be unique for, half an hour, in seconds
     */
    public function uniqueFor(): int
    {
        return 30 * 60;
    }

    public function handle(): void
    {
        if (! app(LicenseSettings::class)->data?->addons?->serviceMonitoring) {
            return;
        }

        ServiceMonitoringTarget::where('report_frequency', $this->frequency)
            ->chunkById(100, function (Collection $serviceMonitoringTargets) {
                foreach ($serviceMonitoringTargets as $serviceMonitoringTarget) {
                    dispatch(new ServiceMonitoringReportNotifyJob($serviceMonitoringTarget));
                }
            });
    }
}
