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

namespace AidingApp\ServiceManagement\Jobs;

use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\ServiceManagement\Enums\MonitorType;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Notifications\ServiceMonitoringNotification;
use App\Features\MonitorTypeFeature;
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
        if (MonitorTypeFeature::active()) {
            match ($this->serviceMonitoringTarget->monitor_type) {
                MonitorType::Availability => $this->handleAvailability(),
                MonitorType::KeywordMatch => $this->handleKeywordMatch(),
            };
        } else {
            $this->handleAvailability();
        }
    }

    /**
     * @param list<string>|null $keywordMatchFailures
     */
    public function handleResponses(int $status, float $responseTime, bool $success, ?array $keywordMatchFailures = null): void
    {
        $historyData = [
            'response' => $status,
            'response_time' => $responseTime,
            'succeeded' => $success,
        ];

        if ($keywordMatchFailures !== null) {
            $historyData['keyword_match_failures'] = $keywordMatchFailures;
        }

        $history = $this->serviceMonitoringTarget->histories()->create($historyData);

        if (! $success) {
            $recipients = $this->serviceMonitoringTarget->users()->get();

            $departmentUsers = $this->serviceMonitoringTarget
                ->departments()
                ->with('users')
                ->get()
                ->pluck('users')
                ->flatten(1);

            $recipients = $recipients->merge($departmentUsers)->unique('id');

            $channel = match (true) {
                $this->serviceMonitoringTarget->is_notified_via_email && $this->serviceMonitoringTarget->is_notified_via_database => 'both',
                $this->serviceMonitoringTarget->is_notified_via_email => MailChannel::class,
                $this->serviceMonitoringTarget->is_notified_via_database => DatabaseChannel::class,
                default => null,
            };

            if (! $channel) {
                return;
            }

            Notification::send($recipients, new ServiceMonitoringNotification($history, $channel));
        }
    }

    protected function handleAvailability(): void
    {
        try {
            $response = Http::maxRedirects(15)
                ->head($this->serviceMonitoringTarget->domain);

            $this->handleResponses($response->status(), $response->transferStats->getTransferTime() ?? 0, $response->status() === 200);
        } catch (ConnectionException $exception) {
            if (Str::doesntContain($exception->getMessage(), 'Could not resolve host')) {
                report($exception);
            }
            $this->handleResponses(523, 0, false);
        }
    }

    protected function handleKeywordMatch(): void
    {
        try {
            $response = Http::maxRedirects(15)
                ->get($this->serviceMonitoringTarget->domain);

            $responseBody = $this->readableResponseBody($response->body());

            $requiredFailures = collect($this->serviceMonitoringTarget->should_contain ?? [])
                ->unique()
                ->reject(fn (string $value): bool => Str::contains($responseBody, $value, true))
                ->map(fn (string $value): string => "Required string not found: {$value}");

            $prohibitedFailures = collect($this->serviceMonitoringTarget->should_not_contain ?? [])
                ->unique()
                ->filter(fn (string $value): bool => Str::contains($responseBody, $value, true))
                ->map(fn (string $value): string => "Prohibited string found: {$value}");

            $keywordMatchFailures = $requiredFailures
                ->concat($prohibitedFailures)
                ->values()
                ->all();

            $success = $response->status() === 200 && $keywordMatchFailures === [];

            $this->handleResponses($response->status(), $response->transferStats->getTransferTime() ?? 0, $success, $keywordMatchFailures);
        } catch (ConnectionException $exception) {
            if (Str::doesntContain($exception->getMessage(), 'Could not resolve host')) {
                report($exception);
            }
            $this->handleResponses(523, 0, false);
        }
    }

    protected function readableResponseBody(string $responseBody): string
    {
        $responseBody = preg_replace('/<\s*(script|style)\b[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $responseBody) ?? $responseBody;
        $responseBody = html_entity_decode(strip_tags($responseBody), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return (string) preg_replace('/\s+/', ' ', $responseBody);
    }
}
