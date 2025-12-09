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

namespace AidingApp\ServiceManagement\Jobs;

use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Notifications\ServiceMonitoringNotification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ServiceMonitoringCheckJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public ServiceMonitoringTarget $serviceMonitoringTarget) {}

    public function uniqueId(): string
    {
        return $this->serviceMonitoringTarget->getKey();
    }

    /**
     * Return the period for which this job should be unique for, its interval plus half an hour, in seconds
     */
    public function uniqueFor(): int
    {
        $seconds = match ($this->serviceMonitoringTarget->frequency) {
            ServiceMonitoringFrequency::FiveMinutes => 5 * 60,
            ServiceMonitoringFrequency::FifteenMinutes => 15 * 60,
            ServiceMonitoringFrequency::ThirtyMinutes => 30 * 60,
            ServiceMonitoringFrequency::OneHour => 60 * 60,
            ServiceMonitoringFrequency::TwentyFourHours => 24 * 60 * 60,
        };

        return $seconds + (30 * 60);
    }

    public function handle(): void
    {
        try {
            $response = Http::maxRedirects(15)
                ->get($this->serviceMonitoringTarget->domain);

            $this->handleResponses($response->status(), $response->transferStats->getTransferTime() ?? 0, $response->status() === 200);
        } catch (ConnectionException $e) {
            if (Str::doesntContain($e->getMessage(), 'Could not resolve host')) {
                report($e);
            }
            $this->handleResponses(523, 0, false);
        }
    }

    public function handleResponses(int $status, float $responseTime, bool $success): void
    {
        $history = $this->serviceMonitoringTarget->histories()->create([
            'response' => $status,
            'response_time' => $responseTime,
            'succeeded' => $success,
        ]);

        if (! $success) {
            $recipients = $this->serviceMonitoringTarget->users()->get();

            $this->serviceMonitoringTarget->teams()->each(function ($team) use (&$recipients) {
                $users = $team->users()->get();
                $recipients = $recipients->merge($users)->unique('id');
            });
            Notification::send($recipients, new ServiceMonitoringNotification($history));
        }
    }
}
