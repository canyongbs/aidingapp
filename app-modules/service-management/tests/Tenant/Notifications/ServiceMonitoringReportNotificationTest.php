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

use AidingApp\Contact\Models\Contact;
use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringReportFrequency;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Notifications\ServiceMonitoringReportNotification;
use App\Models\User;
use App\Settings\DisplaySettings;
use Carbon\CarbonInterface;

it('routes notifications to the expected channels', function (string $channel, array $expectedChannels) {
    $target = ServiceMonitoringTarget::factory()->create();

    $notification = new ServiceMonitoringReportNotification(
        $target,
        $target->report_frequency,
        $channel,
    );

    expect($notification->via(User::factory()->create()))->toBe($expectedChannels);
})->with([
    'database only' => [DatabaseChannel::class, ['database']],
    'mail only' => [MailChannel::class, ['mail']],
    'both' => ['both', ['database', 'mail']],
]);

it('throws an error for an unsupported channel', function () {
    $target = ServiceMonitoringTarget::factory()->create();

    $notification = new ServiceMonitoringReportNotification(
        $target,
        $target->report_frequency,
        'unsupported-channel',
    );

    expect(fn () => $notification->via(User::factory()->create()))
        ->toThrow(InvalidArgumentException::class, 'Unsupported notification channel: unsupported-channel');
});

it('returns N/A statistics when no checks exist in the reporting period', function () {
    $target = ServiceMonitoringTarget::factory()->create(['name' => 'Payment API']);

    $notification = new ServiceMonitoringReportNotification($target, $target->report_frequency, DatabaseChannel::class);
    $databaseMessage = $notification->toDatabase(User::factory()->create());

    expect($databaseMessage['title'])->toContain('Your ' . $target->report_frequency->value . ' service monitor report for Payment API is ready.')
        ->and($databaseMessage['body'])->toContain('Uptime: N/A')
        ->and($databaseMessage['body'])->toContain('Successful checks: 0')
        ->and($databaseMessage['body'])->toContain('Failed checks: 0')
        ->and($databaseMessage['body'])->toContain('No incidents were detected during this reporting period.');
});

it('returns N/A statistics when no checks exist in the reporting period for mail notification', function () {
    $target = ServiceMonitoringTarget::factory()->create(['name' => 'Payment API']);

    $notification = new ServiceMonitoringReportNotification($target, $target->report_frequency, MailChannel::class);
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

    $notification = new ServiceMonitoringReportNotification($target, $target->report_frequency, MailChannel::class);
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

    $notification = new ServiceMonitoringReportNotification($target, $target->report_frequency, MailChannel::class);
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

it('suppresses delivery to a user without confidential access to a confidential target', function () {
    $target = ServiceMonitoringTarget::factory()->confidential()->create();
    $user = User::factory()->create();

    $notification = new ServiceMonitoringReportNotification($target, ServiceMonitoringReportFrequency::Daily, MailChannel::class);

    expect($notification->via($user))->toBe([]);
});

it('delivers to a user granted confidential access to a confidential target', function () {
    $target = ServiceMonitoringTarget::factory()->confidential()->create();
    $user = User::factory()->create();
    $target->confidentialUsers()->attach($user->getKey());

    $notification = new ServiceMonitoringReportNotification($target, ServiceMonitoringReportFrequency::Daily, MailChannel::class);

    expect($notification->via($user))->toBe(['mail']);
});

it('suppresses delivery to a contact without confidential access to a confidential target', function () {
    $target = ServiceMonitoringTarget::factory()->confidential()->create();
    $contact = Contact::factory()->create();

    $notification = new ServiceMonitoringReportNotification($target, ServiceMonitoringReportFrequency::Daily, MailChannel::class);

    expect($notification->via($contact))->toBe([]);
});

it('delivers to a contact granted confidential access to a confidential target', function () {
    $target = ServiceMonitoringTarget::factory()->confidential()->create();
    $contact = Contact::factory()->create();
    $target->confidentialContacts()->attach($contact->getKey());

    $notification = new ServiceMonitoringReportNotification($target, ServiceMonitoringReportFrequency::Daily, MailChannel::class);

    expect($notification->via($contact))->toBe(['mail']);
});

it('restores a notification serialized under the previous payload shape with no frequency property', function () {
    $target = ServiceMonitoringTarget::factory()->create();

    $values = [
        'serviceMonitoringTarget' => $target,
        'channel' => MailChannel::class,
    ];

    $restored = new ServiceMonitoringReportNotification($target, ServiceMonitoringReportFrequency::Weekly, MailChannel::class);
    $restored->__unserialize($values);

    expect($restored->serviceMonitoringTarget->is($target))->toBeTrue()
        ->and($restored->frequency)->toBe(ServiceMonitoringReportFrequency::Monthly)
        ->and($restored->channel)->toBe(MailChannel::class);
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
