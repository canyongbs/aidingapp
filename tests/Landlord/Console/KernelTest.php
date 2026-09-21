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
use Illuminate\Console\Scheduling\Event;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\artisan;
use function Pest\Laravel\travelTo;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Facades\Health;

describe('schedule', function () {
    it('dispatches the tenant fan-out orchestrators on a daily run', function () {
        Queue::fake();

        travelTo(now()->startOfDay());

        artisan('schedule:run');

        Queue::assertPushed(DispatchDeliverEngagementsForEachTenant::class);
        Queue::assertPushed(DispatchEndServiceRequestConversationsForEachTenant::class);
        Queue::assertPushed(DispatchPruneEphemeralMessagesForEachTenant::class);
        Queue::assertPushed(DispatchHealthChecksForEachTenant::class);
        Queue::assertPushed(DispatchPrepareKnowledgeBaseVectorStoreForEachTenant::class);
        Queue::assertPushed(DispatchServiceMonitoringForEachTenant::class, 5);
        Queue::assertPushed(DispatchClosedServiceRequestFeedbackRemindersForEachTenant::class);
        Queue::assertPushed(DispatchStaleDraftServiceRequestAutoSubmissionForEachTenant::class);
        Queue::assertPushed(DispatchStaleCacheTagPruningForEachTenant::class);
        Queue::assertPushed(DispatchUnmatchedInboundCommunicationsForEachTenant::class);
        Queue::assertPushed(DispatchKnowledgeBaseArticleChecksForEachTenant::class);
        Queue::assertPushed(DispatchModelPruningForEachTenant::class);
        Queue::assertPushed(
            DispatchServiceMonitoringReportForEachTenant::class,
            fn (DispatchServiceMonitoringReportForEachTenant $job) => $job->frequency === ServiceMonitoringReportFrequency::Daily,
        );
    });

    it('dispatches the weekly service monitoring report orchestrator on Mondays', function () {
        Queue::fake();

        travelTo(now()->startOfDay()->next(1));

        artisan('schedule:run');

        Queue::assertPushed(
            DispatchServiceMonitoringReportForEachTenant::class,
            fn (DispatchServiceMonitoringReportForEachTenant $job) => $job->frequency === ServiceMonitoringReportFrequency::Weekly,
        );
    });

    it('dispatches the monthly service monitoring report orchestrator on the first of the month', function () {
        Queue::fake();

        travelTo(now()->startOfMonth());

        artisan('schedule:run');

        Queue::assertPushed(
            DispatchServiceMonitoringReportForEachTenant::class,
            fn (DispatchServiceMonitoringReportForEachTenant $job) => $job->frequency === ServiceMonitoringReportFrequency::Monthly,
        );
    });

    it('dispatches only the per-minute tasks on an off-cadence minute', function () {
        Queue::fake();

        travelTo(now()->startOfDay()->setTime(10, 7));

        artisan('schedule:run');

        Queue::assertPushed(GatherAndDispatchSesS3InboundEmails::class);
        Queue::assertPushed(DispatchDeliverEngagementsForEachTenant::class);
        Queue::assertPushed(DispatchEndServiceRequestConversationsForEachTenant::class);
        Queue::assertPushed(DispatchPruneEphemeralMessagesForEachTenant::class);
        Queue::assertPushed(DispatchHealthChecksForEachTenant::class);

        Queue::assertNotPushed(DispatchServiceMonitoringForEachTenant::class);
        Queue::assertNotPushed(DispatchPrepareKnowledgeBaseVectorStoreForEachTenant::class);
        Queue::assertNotPushed(DispatchClosedServiceRequestFeedbackRemindersForEachTenant::class);
        Queue::assertNotPushed(DispatchStaleDraftServiceRequestAutoSubmissionForEachTenant::class);
        Queue::assertNotPushed(DispatchStaleCacheTagPruningForEachTenant::class);
        Queue::assertNotPushed(DispatchServiceMonitoringReportForEachTenant::class);
        Queue::assertNotPushed(DispatchUnmatchedInboundCommunicationsForEachTenant::class);
        Queue::assertNotPushed(DispatchKnowledgeBaseArticleChecksForEachTenant::class);
        Queue::assertNotPushed(DispatchModelPruningForEachTenant::class);
    });

    it('dispatches the five-minute service monitoring orchestrator on a five-minute boundary', function () {
        Queue::fake();

        travelTo(now()->startOfDay()->setTime(10, 5));

        artisan('schedule:run');

        Queue::assertPushed(DispatchPrepareKnowledgeBaseVectorStoreForEachTenant::class);
        Queue::assertPushed(DispatchServiceMonitoringForEachTenant::class, 1);
        Queue::assertPushed(
            DispatchServiceMonitoringForEachTenant::class,
            fn (DispatchServiceMonitoringForEachTenant $job) => $job->frequency === ServiceMonitoringFrequency::FiveMinutes,
        );
    });

    it('dispatches the fifteen-minute service monitoring orchestrator on a fifteen-minute boundary', function () {
        Queue::fake();

        travelTo(now()->startOfDay()->setTime(10, 15));

        artisan('schedule:run');

        Queue::assertPushed(DispatchServiceMonitoringForEachTenant::class, 2);
        Queue::assertPushed(
            DispatchServiceMonitoringForEachTenant::class,
            fn (DispatchServiceMonitoringForEachTenant $job) => $job->frequency === ServiceMonitoringFrequency::FifteenMinutes,
        );
    });

    it('dispatches the thirty-minute service monitoring orchestrator on a thirty-minute boundary', function () {
        Queue::fake();

        travelTo(now()->startOfDay()->setTime(10, 30));

        artisan('schedule:run');

        Queue::assertPushed(DispatchServiceMonitoringForEachTenant::class, 3);
        Queue::assertPushed(
            DispatchServiceMonitoringForEachTenant::class,
            fn (DispatchServiceMonitoringForEachTenant $job) => $job->frequency === ServiceMonitoringFrequency::ThirtyMinutes,
        );
    });

    it('writes the schedule heartbeat to the shared store on each run', function () {
        Queue::fake();

        $check = Health::registeredChecks()->first(fn (Check $registeredCheck) => $registeredCheck instanceof ScheduleCheck);

        assert($check instanceof ScheduleCheck);

        cache()->store('health')->forget($check->getCacheKey());

        travelTo(now()->startOfDay());

        artisan('schedule:run');

        expect(cache()->store('health')->has($check->getCacheKey()))->toBeTrue();

        cache()->store('health')->forget($check->getCacheKey());
    });

    it('records the schedule heartbeat via the liveness beacon', function () {
        $path = storage_path('framework/schedule-heartbeat');

        @unlink($path);
        expect(file_exists($path))->toBeFalse();

        $beacon = collect(app(Kernel::class)->resolveConsoleSchedule()->events())
            ->firstWhere('description', 'Schedule Liveness Beacon');

        assert($beacon instanceof Event);

        $beacon->run(app());

        expect(file_exists($path))->toBeTrue();

        @unlink($path);
    });
});
