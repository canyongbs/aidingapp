<?php

namespace App\Listeners;

use App\Multitenancy\Events\NewTenantSetupComplete;
use App\Multitenancy\Events\NewTenantSetupFailure;
use App\Services\Olympus;
use App\Settings\OlympusSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Spatie\Multitenancy\Landlord;

class InformOlympusOfDeploymentEvent implements ShouldQueue, NotTenantAware
{
    public function handle(NewTenantSetupComplete|NewTenantSetupFailure $event): void
    {
        $isConfigured = Landlord::execute(function (): bool {
            $settings = app(OlympusSettings::class);

            return ! is_null($settings->key);
        });

        if (! $isConfigured) {
            return;
        }

        $tenantId = $event->tenant->getKey();

        app(Olympus::class)->makeRequest()
            ->asJson()
            ->post(
                url: "/api/deployment/{$tenantId}/report-event",
                data: match (true) {
                    $event instanceof NewTenantSetupComplete => [
                        'type' => 'complete',
                        'occurred_at' => now()->toDateTimeString('millisecond'),
                    ],
                    $event instanceof NewTenantSetupFailure => [
                        'type' => 'error',
                        'occurred_at' => now()->toDateTimeString('millisecond'),
                        'message' => $event->exception->getMessage(),
                    ],
                }
            )
            ->throw();
    }
}
