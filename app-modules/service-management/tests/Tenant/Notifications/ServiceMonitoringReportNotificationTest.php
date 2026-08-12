<?php

use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringReportFrequency;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Notifications\ServiceMonitoringReportNotification;
use App\Models\User;
use App\Settings\DisplaySettings;
use Carbon\CarbonInterface;

it('routes notifications to the expected channels', function (string $channel, array $expectedChannels) {
    $notification = new ServiceMonitoringReportNotification(
        ServiceMonitoringTarget::factory()->create(),
        $channel,
    );

    expect($notification->via(User::factory()->create()))->toBe($expectedChannels);
})->with([
    'database only' => [DatabaseChannel::class, ['database']],
    'mail only' => [MailChannel::class, ['mail']],
    'both' => ['both', ['database', 'mail']],
]);

it('throws an error for an unsupported channel', function () {
    $notification = new ServiceMonitoringReportNotification(
        ServiceMonitoringTarget::factory()->create(),
        'unsupported-channel',
    );

    expect(fn () => $notification->via(User::factory()->create()))
        ->toThrow(InvalidArgumentException::class, 'Unsupported notification channel: unsupported-channel');
});

it('returns N/A statistics when no checks exist in the reporting period', function () {
    $target = ServiceMonitoringTarget::factory()->create(['name' => 'Payment API']);

    $notification = new ServiceMonitoringReportNotification($target, DatabaseChannel::class);
    $databaseMessage = $notification->toDatabase(User::factory()->create());

    expect($databaseMessage['title'])->toContain('Your ' . $target->report_frequency->value . ' service monitor report for Payment API is ready.')
        ->and($databaseMessage['body'])->toContain('Uptime: N/A')
        ->and($databaseMessage['body'])->toContain('Successful checks: 0')
        ->and($databaseMessage['body'])->toContain('Failed checks: 0')
        ->and($databaseMessage['body'])->toContain('No incidents were detected during this reporting period.');
});

it('returns N/A statistics when no checks exist in the reporting period for mail notification', function () {
    $target = ServiceMonitoringTarget::factory()->create(['name' => 'Payment API']);

    $notification = new ServiceMonitoringReportNotification($target, MailChannel::class);
    $mailMessage = $notification->toMail(User::factory()->create())->toArray();
    $viewData = $mailMessage['viewData'];

    expect($mailMessage['subject'])->toBe($target->report_frequency->getLabel() . ' Service Monitor Report: Payment API')
        ->and($viewData['uptimePercentage'])->toBe('N/A')
        ->and($viewData['successfulChecks'])->toBe(0)
        ->and($viewData['failedChecks'])->toBe(0)
        ->and($viewData['averageResponseTime'])->toBe('N/A')
        ->and($viewData['totalDowntime'])->toBe('N/A')
        ->and($viewData['incidentSummary'])->toBe('No incidents were detected during this reporting period.');
});

it('builds expected report statistics and incident summary in the mail payload', function () {
    $target = ServiceMonitoringTarget::factory()->create(['name' => 'External Status API']);

    [$localStart, $localEnd] = match ($target->report_frequency) {
        ServiceMonitoringReportFrequency::Daily => [
            now()->copy()->subDay()->startOfDay(),
            now()->copy()->subDay()->endOfDay(),
        ],
        ServiceMonitoringReportFrequency::Weekly => [
            now()->copy()->subWeek()->startOfWeek(CarbonInterface::MONDAY)->startOfDay(),
            now()->copy()->subWeek()->endOfWeek(CarbonInterface::SUNDAY)->endOfDay(),
        ],
        ServiceMonitoringReportFrequency::Monthly => [
            now()->copy()->subMonthNoOverflow()->startOfMonth()->startOfDay(),
            now()->copy()->subMonthNoOverflow()->endOfMonth()->endOfDay(),
        ],
    };

    createHistoryAt($target, true, 0.50, $localStart->copy()->addHours(1)->utc());
    createHistoryAt($target, true, 1.00, $localStart->copy()->addHours(12)->utc());
    createHistoryAt($target, false, 1.50, $localEnd->copy()->subHour()->utc());

    $notification = new ServiceMonitoringReportNotification($target, MailChannel::class);
    $mailMessage = $notification->toMail(User::factory()->create())->toArray();
    $viewData = $mailMessage['viewData'];

    expect($mailMessage['subject'])->toBe($target->report_frequency->getLabel() . ' Service Monitor Report: External Status API')
        ->and($viewData['uptimePercentage'])->toBe('66.67%')
        ->and($viewData['successfulChecks'])->toBe(2)
        ->and($viewData['failedChecks'])->toBe(1)
        ->and($viewData['averageResponseTime'])->toBe('1.00 s')
        ->and($viewData['totalDowntime'])->toBe('33.33%')
        ->and($viewData['incidentSummary'])->toBe('1 incident was detected during this reporting period.');
});

it('uses tenant display timezone boundaries for daily reports', function () {
    app(DisplaySettings::class)->timezone = 'America/New_York';
    app(DisplaySettings::class)->save();

    $target = ServiceMonitoringTarget::factory()->create();

    $localNow = now()->setTimezone('America/New_York');

    [$localStart, $localEnd] = match ($target->report_frequency) {
        ServiceMonitoringReportFrequency::Daily => [
            $localNow->copy()->subDay()->startOfDay(),
            $localNow->copy()->subDay()->endOfDay(),
        ],
        ServiceMonitoringReportFrequency::Weekly => [
            $localNow->copy()->subWeek()->startOfWeek(CarbonInterface::MONDAY)->startOfDay(),
            $localNow->copy()->subWeek()->endOfWeek(CarbonInterface::SUNDAY)->endOfDay(),
        ],
        ServiceMonitoringReportFrequency::Monthly => [
            $localNow->copy()->subMonthNoOverflow()->startOfMonth()->startOfDay(),
            $localNow->copy()->subMonthNoOverflow()->endOfMonth()->endOfDay(),
        ],
    };

    createHistoryAt($target, true, 0.50, $localStart->copy()->utc());
    createHistoryAt($target, false, 1.50, $localEnd->copy()->utc());
    createHistoryAt($target, true, 2.00, $localStart->copy()->utc()->subSecond());
    createHistoryAt($target, false, 2.50, $localEnd->copy()->utc()->addSecond());

    $notification = new ServiceMonitoringReportNotification($target, MailChannel::class);
    $mailMessage = $notification->toMail(User::factory()->create())->toArray();
    $viewData = $mailMessage['viewData'];

    expect($viewData['timezone'])->toBe('America/New_York')
        ->and($viewData['successfulChecks'])->toBe(1)
        ->and($viewData['failedChecks'])->toBe(1)
        ->and($viewData['uptimePercentage'])->toBe('50%')
        ->and($viewData['totalDowntime'])->toBe('50%')
        ->and($viewData['averageResponseTime'])->toBe('1.00 s')
        ->and($viewData['incidentSummary'])->toBe('1 incident was detected during this reporting period.');
});

function createHistoryAt(
    ServiceMonitoringTarget $target,
    bool $succeeded,
    float $responseTime,
    CarbonInterface $createdAt,
): void {
    $history = $target->histories()->create([
        'response' => $succeeded ? 200 : 500,
        'response_time' => $responseTime,
        'succeeded' => $succeeded,
    ]);

    $history->forceFill([
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ])->saveQuietly();
}
