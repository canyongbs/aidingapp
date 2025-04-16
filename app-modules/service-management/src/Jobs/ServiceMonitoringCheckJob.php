<?php

namespace AidingApp\ServiceManagement\Jobs;

use AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Notifications\ServiceMonitoringNotification;
use App\Models\User;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ServiceMonitoringCheckJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public ServiceMonitoringTarget $serviceMonitoringTarget)
    {}

    public function handle(): void
    {
        try {
            $response = Http::get($this->serviceMonitoringTarget->domain);

            new HistoricalServiceMonitoring([
                'response' => $response->status(),
                'successful' => $response->status() === 200,
                'service_monitoring_target_id' => $this->serviceMonitoringTarget->id,
            ]);
    
            if($response->status() !== 200) {
                /** @var Collection<int,User> $recipients */
                $recipients = $this->serviceMonitoringTarget->users()->get();

                $this->serviceMonitoringTarget->teams()->each(function ($team) use ($recipients) {
                    $recipients->concat($team->users()->get())->unique();
                });

                $recipients->each(fn($user) => $user->notify(new ServiceMonitoringNotification()));
            }
        } catch (Exception $e) {
            // ???
        }
    }
}
