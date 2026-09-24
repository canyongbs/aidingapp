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

namespace App\Console;

use AidingApp\Ai\Jobs\DispatchPrepareKnowledgeBaseVectorStoreForEachTenant;
use AidingApp\Engagement\Jobs\DispatchDeliverEngagementsForEachTenant;
use AidingApp\Engagement\Jobs\DispatchUnmatchedInboundCommunicationsForEachTenant;
use AidingApp\Engagement\Jobs\GatherAndDispatchSesS3InboundEmails;
use AidingApp\InAppCommunication\Jobs\DispatchPruneEphemeralMessagesForEachTenant;
use AidingApp\KnowledgeBase\Jobs\DispatchKnowledgeBaseArticleChecksForEachTenant;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringReportFrequency;
use AidingApp\ServiceManagement\Jobs\DispatchClosedServiceRequestFeedbackRemindersForEachTenant;
use AidingApp\ServiceManagement\Jobs\DispatchEndServiceRequestConversationsForEachTenant;
use AidingApp\ServiceManagement\Jobs\DispatchServiceMonitoringForEachTenant;
use AidingApp\ServiceManagement\Jobs\DispatchServiceMonitoringReportForEachTenant;
use AidingApp\ServiceManagement\Jobs\DispatchStaleDraftServiceRequestAutoSubmissionForEachTenant;
use App\Jobs\DispatchHealthChecksForEachTenant;
use App\Jobs\DispatchModelPruningForEachTenant;
use App\Jobs\DispatchStaleCacheTagPruningForEachTenant;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new GatherAndDispatchSesS3InboundEmails())
            ->everyMinute()
            ->name('Gather and Dispatch SES S3 Inbound Emails')
            ->onOneServer();

        $schedule->job(new DispatchDeliverEngagementsForEachTenant())
            ->everyMinute()
            ->onOneServer();

        $schedule->job(new DispatchEndServiceRequestConversationsForEachTenant())
            ->everyMinute()
            ->onOneServer();

        $schedule->job(new DispatchPruneEphemeralMessagesForEachTenant())
            ->everyMinute()
            ->onOneServer();

        $schedule->job(new DispatchHealthChecksForEachTenant())
            ->everyMinute()
            ->onOneServer();

        $schedule->job(new DispatchPrepareKnowledgeBaseVectorStoreForEachTenant())
            ->everyFiveMinutes()
            ->onOneServer();

        $schedule->job(new DispatchServiceMonitoringForEachTenant(ServiceMonitoringFrequency::FiveMinutes))
            ->everyFiveMinutes()
            ->name('Dispatch Service Monitoring For Each Tenant (Five Minutes)')
            ->onOneServer();

        $schedule->job(new DispatchServiceMonitoringForEachTenant(ServiceMonitoringFrequency::FifteenMinutes))
            ->everyFifteenMinutes()
            ->name('Dispatch Service Monitoring For Each Tenant (Fifteen Minutes)')
            ->onOneServer();

        $schedule->job(new DispatchServiceMonitoringForEachTenant(ServiceMonitoringFrequency::ThirtyMinutes))
            ->everyThirtyMinutes()
            ->name('Dispatch Service Monitoring For Each Tenant (Thirty Minutes)')
            ->onOneServer();

        $schedule->job(new DispatchServiceMonitoringForEachTenant(ServiceMonitoringFrequency::OneHour))
            ->hourly()
            ->name('Dispatch Service Monitoring For Each Tenant (One Hour)')
            ->onOneServer();

        $schedule->job(new DispatchServiceMonitoringForEachTenant(ServiceMonitoringFrequency::TwentyFourHours))
            ->daily()
            ->name('Dispatch Service Monitoring For Each Tenant (Twenty Four Hours)')
            ->onOneServer();

        $schedule->job(new DispatchClosedServiceRequestFeedbackRemindersForEachTenant())
            ->hourly()
            ->onOneServer();

        $schedule->job(new DispatchStaleDraftServiceRequestAutoSubmissionForEachTenant())
            ->hourly()
            ->onOneServer();

        $schedule->job(new DispatchStaleCacheTagPruningForEachTenant())
            ->hourly()
            ->onOneServer();

        $schedule->job(new DispatchServiceMonitoringReportForEachTenant(ServiceMonitoringReportFrequency::Daily))
            ->daily()
            ->name('Dispatch Service Monitoring Report For Each Tenant (Daily)')
            ->onOneServer();

        $schedule->job(new DispatchServiceMonitoringReportForEachTenant(ServiceMonitoringReportFrequency::Weekly))
            ->weekly()
            ->mondays()
            ->name('Dispatch Service Monitoring Report For Each Tenant (Weekly)')
            ->onOneServer();

        $schedule->job(new DispatchServiceMonitoringReportForEachTenant(ServiceMonitoringReportFrequency::Monthly))
            ->monthly()
            ->name('Dispatch Service Monitoring Report For Each Tenant (Monthly)')
            ->onOneServer();

        $schedule->job(new DispatchUnmatchedInboundCommunicationsForEachTenant())
            ->daily()
            ->onOneServer();

        $schedule->job(new DispatchKnowledgeBaseArticleChecksForEachTenant())
            ->daily()
            ->onOneServer();

        $schedule->job(new DispatchModelPruningForEachTenant())
            ->daily()
            ->onOneServer();

        $schedule->command('health:queue-check-heartbeat')
            ->everyMinute()
            ->onOneServer();

        $schedule->command('health:schedule-check-heartbeat')
            ->everyMinute()
            ->onOneServer();

        // Registered last so it only records once a full schedule run has been dispatched.
        $schedule->call(fn () => touch(storage_path('framework/schedule-heartbeat')))
            ->everyMinute()
            ->name('Schedule Liveness Beacon')
            // @phpstan-ignore method.notFound (sentryMonitor is a macro registered by sentry/sentry-laravel)
            ->sentryMonitor(monitorSlug: 'aidingapp-scheduler-liveness', checkInMargin: 5, failureIssueThreshold: 5);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
