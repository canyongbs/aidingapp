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
use AidingApp\ServiceManagement\Enums\AuthType;
use AidingApp\ServiceManagement\Enums\MonitorType;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Notifications\ServiceMonitoringNotification;
use AidingApp\ServiceManagement\Services\ChallengePageDetector;
use AidingApp\ServiceManagement\Services\HtmlTextExtractor;
use App\Features\ServiceMonitoringAuthTypeFeature;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use InvalidArgumentException;

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
        match ($this->serviceMonitoringTarget->monitor_type) {
            MonitorType::Availability => $this->handleAvailability(),
            MonitorType::KeywordMatch => $this->handleKeywordMatch(),
            MonitorType::ApiEndpoint => $this->handleApiEndpoint(),
        };
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

    /**
     * Build the base HTTP client for this monitor's requests, applying auth so every
     * request verb (current and future) inherits it without repeating the logic.
     */
    protected function buildRequest(bool $followRedirects = true): PendingRequest
    {
        $request = ($followRedirects ? Http::maxRedirects(15) : Http::withoutRedirecting())
            ->connectTimeout(10)
            ->timeout($this->requestTimeoutInSeconds());

        if (ServiceMonitoringAuthTypeFeature::active() && $this->serviceMonitoringTarget->auth_type === AuthType::Basic) {
            $request = $request->withBasicAuth(
                $this->serviceMonitoringTarget->auth_username ?? '',
                $this->serviceMonitoringTarget->auth_password ?? '',
            );
        }

        return $request;
    }

    /**
     * A fixed timeout would either cut off a request before a generous configured
     * max_latency_ms could ever be evaluated, or leave a queue worker blocked far longer than
     * intended for a monitor with no latency threshold set. Scale the request timeout to the
     * configured threshold (plus a buffer for the max_latency_ms check itself to run) when one
     * exists, and fall back to a fixed default otherwise (matching the timeout other
     * external-URL check jobs in this app use, e.g. CheckKnowledgeBaseArticleLinksJob).
     */
    protected function requestTimeoutInSeconds(): int
    {
        if ($this->serviceMonitoringTarget->monitor_type === MonitorType::ApiEndpoint
            && $this->serviceMonitoringTarget->is_max_latency_enabled
            && $this->serviceMonitoringTarget->max_latency_ms) {
            return (int) ceil($this->serviceMonitoringTarget->max_latency_ms / 1000) + 5;
        }

        return 15;
    }

    protected function handleAvailability(): void
    {
        try {
            $response = $this->buildRequest($this->serviceMonitoringTarget->follow_redirection)
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
        if (blank($this->serviceMonitoringTarget->should_contain) && blank($this->serviceMonitoringTarget->should_not_contain)) {
            $this->handleResponses(0, 0, false, ['No keyword match values were configured.']);

            return;
        }

        try {
            $response = $this->buildRequest($this->serviceMonitoringTarget->follow_redirection)
                ->get($this->serviceMonitoringTarget->domain);

            if (filled($challengePageFailure = (new ChallengePageDetector())->detect($response->headers(), $response->body()))) {
                $this->handleResponses(
                    $response->status(),
                    $response->transferStats->getTransferTime() ?? 0,
                    false,
                    [$challengePageFailure],
                );

                return;
            }

            if (! $this->hasReadableContentType($response->header('Content-Type'))) {
                $this->handleResponses(
                    $response->status(),
                    $response->transferStats->getTransferTime() ?? 0,
                    false,
                    ['The response has an unreadable content type.'],
                );

                return;
            }

            $contentType = $response->header('Content-Type');
            $responseBody = (new HtmlTextExtractor())->extract(
                $response->body(),
                $this->responseCharset($contentType),
            );

            $requiredFailures = collect($this->serviceMonitoringTarget->should_contain ?? [])
                ->unique()
                ->reject(fn (string $value): bool => Str::contains($responseBody, HtmlTextExtractor::normalizeWhitespace($value), true))
                ->map(fn (string $value): string => "Required string not found: {$value}");

            $prohibitedFailures = collect($this->serviceMonitoringTarget->should_not_contain ?? [])
                ->unique()
                ->filter(fn (string $value): bool => Str::contains($responseBody, HtmlTextExtractor::normalizeWhitespace($value), true))
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

    protected function handleApiEndpoint(): void
    {
        try {
            $request = $this->buildRequest($this->serviceMonitoringTarget->follow_redirection);

            $headers = collect($this->serviceMonitoringTarget->request_headers ?? [])
                ->filter(fn (array $header): bool => filled($header['name'] ?? null))
                ->mapWithKeys(fn (array $header): array => [$header['name'] => $header['value'] ?? ''])
                ->all();

            if ($headers !== []) {
                $request = $request->withHeaders($headers);
            }

            $httpMethod = $this->serviceMonitoringTarget->http_method;

            $options = [];

            if ($httpMethod->supportsRequestBody() && filled($this->serviceMonitoringTarget->request_body)) {
                // Send the already-validated JSON string as the raw body rather than
                // json_decode()-ing then letting Guzzle's 'json' option re-encode it: that
                // round trip can change a valid payload (e.g. '{}' becomes '[]' since PHP has no
                // empty-object/empty-array distinction, and large integers can lose precision).
                $options = $this->serviceMonitoringTarget->is_request_body_json
                    ? [
                        'body' => $this->serviceMonitoringTarget->request_body,
                        'headers' => ['Content-Type' => 'application/json'],
                    ]
                    : [
                        'body' => $this->serviceMonitoringTarget->request_body,
                        'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                    ];
            }

            $response = $request->send($httpMethod->value, $this->serviceMonitoringTarget->domain, $options);

            $responseTime = $response->transferStats->getTransferTime() ?? 0;

            $failures = [];

            // ValidHttpStatusCodes deliberately normalizes a scalar into an array at the
            // validation layer (see its docblock), but doesn't guarantee the persisted value
            // is one -- guard here too so a stored scalar can't crash the queued check. PHPStan
            // trusts the model cast's declared array type, but that's exactly what a value
            // written outside Eloquent (e.g. direct database access) can violate.
            $storedStatusCodes = $this->serviceMonitoringTarget->successful_status_codes ?? [];
            // @phpstan-ignore function.alreadyNarrowedType
            $successfulStatusCodes = array_map('intval', is_array($storedStatusCodes) ? $storedStatusCodes : [$storedStatusCodes]);

            if (! in_array($response->status(), $successfulStatusCodes, true)) {
                $failures[] = "Unexpected status code: {$response->status()}";
            }

            // $responseTime is in seconds (Guzzle's TransferStats convention, matching how response_time
            // is stored and reported everywhere else); max_latency_ms is milliseconds, so convert before comparing.
            if ($this->serviceMonitoringTarget->is_max_latency_enabled && $this->exceedsMaxLatency($responseTime)) {
                $failures[] = "Response exceeded maximum allowed latency of {$this->serviceMonitoringTarget->max_latency_ms}ms";
            }

            $this->handleResponses($response->status(), $responseTime, $failures === [], $failures);
        } catch (ConnectionException $exception) {
            if (Str::doesntContain($exception->getMessage(), 'Could not resolve host')) {
                report($exception);
            }
            $this->handleResponses(523, 0, false);
        } catch (InvalidArgumentException $exception) {
            // Thrown by the HTTP client when a configured header name/value isn't valid to send over
            // the wire. Form validation prevents saving one this way going forward, but the record's
            // headers could still end up invalid some other way (e.g. direct database access), so
            // record it as a failed check rather than letting the job itself fail/retry.
            $this->handleResponses(0, 0, false, ["Invalid request configuration: {$exception->getMessage()}"]);
        }
    }

    /**
     * Extracted so the millisecond/second unit conversion can be tested in isolation:
     * transferStats reports response time in seconds, while max_latency_ms is milliseconds.
     */
    protected function exceedsMaxLatency(float $responseTimeInSeconds): bool
    {
        return ($responseTimeInSeconds * 1000) > $this->serviceMonitoringTarget->max_latency_ms;
    }

    protected function hasReadableContentType(?string $contentType): bool
    {
        if (blank($contentType)) {
            return true;
        }

        $mediaType = Str::lower(trim(explode(';', $contentType, 2)[0]));

        return str_starts_with($mediaType, 'text/')
            || in_array($mediaType, [
                'application/ecmascript',
                'application/javascript',
                'application/json',
                'application/ld+json',
                'application/xml',
                'application/xhtml+xml',
                'application/yaml',
                'application/x-yaml',
            ], true)
            || str_ends_with($mediaType, '+json')
            || str_ends_with($mediaType, '+xml');
    }

    protected function responseCharset(?string $contentType): ?string
    {
        if (blank($contentType)) {
            return null;
        }

        preg_match('/(?:^|;)\s*charset\s*=\s*([\w.-]+)/i', $contentType, $matches);

        return $matches[1] ?? null;
    }
}
