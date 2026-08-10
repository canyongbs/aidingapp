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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ServiceMonitoringReportNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * @var array<CarbonInterface>
     **/
    private ?array $reportPeriod = null;

    /**
     * @var array<int|string>
     **/
    private ?array $statistics = null;

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

    public function toMail(User|Contact $notifiable): MailMessage
    {
        [$reportPeriodStart, $reportPeriodEnd] = $this->getReportPeriod();
        $stats = $this->getStatistics();

        return MailMessage::make()
            ->subject(($this->serviceMonitoringTarget->report_frequency ?? ServiceMonitoringReportFrequency::Monthly)->getLabel() . ' Service Monitor Report: ' . $this->serviceMonitoringTarget->name)
            ->markdown('service-management::mail.service-monitoring-report', [
                'serviceMonitoringTarget' => $this->serviceMonitoringTarget,
                'reportPeriodStart' => $reportPeriodStart->format('M j, Y g:i a (T)'),
                'reportPeriodEnd' => $reportPeriodEnd->format('M j, Y g:i a (T)'),
                'uptimePercentage' => $stats['uptime_percentage'],
                'successfulChecks' => $stats['successful_checks'],
                'failedChecks' => $stats['failed_checks'],
                'averageResponseTime' => $stats['average_response_time'],
                'totalDowntime' => $stats['downtime_percentage'],
                'incidentSummary' => $this->getIncidentSummary(),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(User|Contact $notifiable): array
    {
        $stats = $this->getStatistics();

        return Notification::make()
            ->title('Your ' . Str::lower($this->serviceMonitoringTarget->report_frequency->value ?? ServiceMonitoringReportFrequency::Monthly->value) . ' service monitor report for ' . $this->serviceMonitoringTarget->name . ' is ready.')
            ->body(
                'Uptime: ' . $stats['uptime_percentage'] . "\n" .
                'Successful checks: ' . $stats['successful_checks'] . "\n" .
                'Failed checks: ' . $stats['failed_checks'] . "\n" .
                'Incident summary: ' . $this->getIncidentSummary()
            )
            ->getDatabaseMessage();
    }

    /**
     * @return array<CarbonInterface>
     */
    private function getReportPeriod(): array
    {
        if ($this->reportPeriod !== null) {
            return $this->reportPeriod;
        }

        $timezone = Tenant::current()?->getTimezone() ?? config('app.timezone');
        $now = now()->setTimezone($timezone);

        $reportFrequency = $this->serviceMonitoringTarget->report_frequency ?? ServiceMonitoringReportFrequency::Monthly;

        $this->reportPeriod = match ($reportFrequency) {
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

        return $this->reportPeriod;
    }

    /**
     * @return array<string, mixed>
     */
    private function getStatistics(): array
    {
        if ($this->statistics !== null) {
            return $this->statistics;
        }

        [$reportPeriodStart, $reportPeriodEnd] = $this->getReportPeriod();

        $result = DB::table('historical_service_monitorings')
            ->where('service_monitoring_target_id', $this->serviceMonitoringTarget->id)
            ->whereBetween('created_at', [$reportPeriodStart, $reportPeriodEnd])
            ->selectRaw('
                COUNT(*) as total_checks,
                SUM(CASE WHEN succeeded = true THEN 1 ELSE 0 END) as successful_checks,
                SUM(CASE WHEN succeeded = false THEN 1 ELSE 0 END) as failed_checks,
                AVG(response_time) as average_response_time
            ')
            ->first();

        $totalChecks = (int) ($result->total_checks ?? 0);
        $failedChecks = (int) ($result->failed_checks ?? 0);
        $successfulChecks = (int) ($result->successful_checks ?? 0);

        // Calculate downtime percentage
        if ($totalChecks === 0) {
            $downtimePercentage = 'N/A';
        } else {
            $downtimePercentage = round((($failedChecks / $totalChecks) * 100), 2) . '%';
        }

        // Calculate uptime percentage
        if ($totalChecks === 0) {
            $uptimePercentage = 'N/A';
        } else {
            $uptimePercentage = round((($successfulChecks / $totalChecks) * 100), 2) . '%';
        }

        // Format average response time
        if ($result->average_response_time === null) {
            $averageResponseTime = 'N/A';
        } else {
            $averageResponseTime = number_format((float) $result->average_response_time, 2) . ' s';
        }

        $this->statistics = [
            'successful_checks' => $successfulChecks,
            'failed_checks' => $failedChecks,
            'average_response_time' => $averageResponseTime,
            'downtime_percentage' => $downtimePercentage,
            'uptime_percentage' => $uptimePercentage,
        ];

        return $this->statistics;
    }

    private function getIncidentSummary(): string
    {
        $incidentCount = $this->getStatistics()['failed_checks'];

        if ($incidentCount === 0) {
            return 'No incidents were detected during this reporting period.';
        }

        return $incidentCount . str('incident')->plural($incidentCount) . ($incidentCount === 1 ? ' was' : ' were') . ' detected during this reporting period.';
    }
}
