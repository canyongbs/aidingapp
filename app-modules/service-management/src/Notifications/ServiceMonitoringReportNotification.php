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

namespace AidingApp\ServiceManagement\Notifications;

use AidingApp\Contact\Models\Contact;
use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringReportFrequency;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Models\Tenant;
use App\Models\User;
use Carbon\CarbonInterface;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ServiceMonitoringReportNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ServiceMonitoringTarget $serviceMonitoringTarget, public string $channel) {}

    /**
     * @return array<int, string>
     */
    public function via(User|Contact $notifiable): array
    {
        return match ($this->channel) {
            DatabaseChannel::class => ['database'],
            MailChannel::class => ['mail'],
            'both' => ['database', 'mail'],
            default => throw new InvalidArgumentException("Unsupported notification channel: {$this->channel}"),
        };
    }

    public function toMail(User $notifiable): MailMessage
    {
        [$reportPeriodStart, $reportPeriodEnd] = $this->getReportPeriod();

        return MailMessage::make()
            ->subject(($this->serviceMonitoringTarget->report_frequency ?? ServiceMonitoringReportFrequency::Monthly)->getLabel() . ' Service Monitor Report: ' . $this->serviceMonitoringTarget->name)
            ->markdown('service-management::mail.service-monitoring-report', [
                'serviceMonitoringTarget' => $this->serviceMonitoringTarget,
                'reportPeriodStart' => $reportPeriodStart,
                'reportPeriodEnd' => $reportPeriodEnd,
                'uptimePercentage' => $this->serviceMonitoringTarget->getUptimePercentage($this->getUptimeDays()),
                'successfulChecks' => $this->getSuccessfulChecks(),
                'failedChecks' => $this->getFailedChecks(),
                'averageResponseTime' => $this->getAverageResponseTime(),
                'totalDowntime' => $this->getTotalDowntime(),
                'incidentSummary' => $this->getIncidentSummary(),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(User $notifiable): array
    {
        return Notification::make()
            ->title('Your ' . Str::lower($this->serviceMonitoringTarget->report_frequency->value ?? ServiceMonitoringReportFrequency::Monthly->value) . ' service monitor report for ' . $this->serviceMonitoringTarget->name . ' is ready.')
            ->body(
                'Uptime: ' . $this->serviceMonitoringTarget->getUptimePercentage($this->getUptimeDays()) . "\n" .
                'Successful checks: ' . $this->getSuccessfulChecks() . "\n" .
                'Failed checks: ' . $this->getFailedChecks() . "\n" .
                'Incident summary: ' . $this->getIncidentSummary()
            )
            ->getDatabaseMessage();
    }

    /**
     * @return array<string>
     */
    private function getReportPeriod(): array
    {
        $timezone = Tenant::current()?->getTimezone() ?? config('app.timezone');
        $now = now()->setTimezone($timezone);

        $reportFrequency = $this->serviceMonitoringTarget->report_frequency ?? ServiceMonitoringReportFrequency::Monthly;

        [$start, $end] = match ($reportFrequency) {
            ServiceMonitoringReportFrequency::Daily => [
                $now->copy()->subDay()->startOfDay(),
                $now->copy()->subDay()->endOfDay(),
            ],
            ServiceMonitoringReportFrequency::Weekly => [
                $now->copy()->subWeek()->startOfWeek(CarbonInterface::MONDAY)->startOfDay(),
                $now->copy()->subWeek()->endOfWeek(CarbonInterface::SUNDAY)->endOfDay(),
            ],
            ServiceMonitoringReportFrequency::Monthly => [
                $now->copy()->subMonthNoOverflow()->startOfMonth()->startOfDay(),
                $now->copy()->subMonthNoOverflow()->endOfMonth()->endOfDay(),
            ],
        };

        return [
            $start->format('M j, Y g:i a (T)'),
            $end->format('M j, Y g:i a (T)'),
        ];
    }

    private function getUptimeDays(): int
    {
        $timezone = Tenant::current()?->getTimezone() ?? config('app.timezone');
        $now = now()->setTimezone($timezone);
        $reportFrequency = $this->serviceMonitoringTarget->report_frequency ?? ServiceMonitoringReportFrequency::Monthly;

        return match ($reportFrequency) {
            ServiceMonitoringReportFrequency::Daily => 1,
            ServiceMonitoringReportFrequency::Weekly => 7,
            ServiceMonitoringReportFrequency::Monthly => $now->copy()->subMonthNoOverflow()->daysInMonth,
        };
    }

    private function getSuccessfulChecks(): int
    {
        return $this->serviceMonitoringTarget
            ->histories()
            ->where('created_at', '>=', now()->subDays($this->getUptimeDays()))
            ->where('succeeded', true)
            ->count();
    }

    private function getFailedChecks(): int
    {
        return $this->serviceMonitoringTarget
            ->histories()
            ->where('created_at', '>=', now()->subDays($this->getUptimeDays()))
            ->where('succeeded', false)
            ->count();
    }

    private function getAverageResponseTime(): string
    {
        $averageResponseTime = $this->serviceMonitoringTarget
            ->histories()
            ->where('created_at', '>=', now()->subDays($this->getUptimeDays()))
            ->avg('response_time');

        if ($averageResponseTime === null) {
            return 'N/A';
        }

        return number_format((float) $averageResponseTime, 2) . ' s';
    }

    private function getTotalDowntime(): string
    {
        $serviceChecks = $this->serviceMonitoringTarget
            ->histories()
            ->where('created_at', '>=', now()->subDays($this->getUptimeDays()))
            ->orderBy('created_at')
            ->get();

        if ($serviceChecks->isEmpty() || now()->subDays($this->getUptimeDays())->diffInDays($serviceChecks->first()->created_at) > 1) {
            return 'N/A';
        }

        $downtimeChecks = $serviceChecks->where('succeeded', false);

        $percentage = ($downtimeChecks->count() / $serviceChecks->count()) * 100;

        return ((int) $percentage === $percentage ? (int) $percentage : round($percentage, 1)) . '%';
    }

    private function getIncidentSummary(): string
    {
        $serviceChecks = $this->serviceMonitoringTarget
            ->histories()
            ->where('created_at', '>=', now()->subDays($this->getUptimeDays()))
            ->where('succeeded', false)
            ->get();

        if ($serviceChecks->isEmpty()) {
            return 'No incidents were detected during this reporting period.';
        }

        return $serviceChecks->count() . str('incident')->plural($serviceChecks->count()) . ' were detected during this reporting period.';
    }
}
