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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\Portal\Actions\ResolvePortalDisplayTimezone;
use AidingApp\Portal\DataTransferObjects\ServiceMonitorData;
use AidingApp\Portal\DataTransferObjects\ServiceMonitorSummaryData;
use AidingApp\Portal\Enums\ServiceMonitorStatusSort;
use AidingApp\ServiceManagement\Actions\GetServiceMonitoringStatusHistory;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringHistoryPeriod;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringStatus;
use AidingApp\ServiceManagement\Models\Scopes\WithCurrentStatus;
use AidingApp\ServiceManagement\Models\Scopes\WithUptimePercentages;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Http\Controllers\Controller;
use App\Settings\LicenseSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ServiceMonitorStatusController extends Controller
{
    public function __invoke(Request $request, GetServiceMonitoringStatusHistory $getStatusHistory): JsonResponse
    {
        abort_unless(resolve(LicenseSettings::class)->data?->addons->serviceMonitoring, Response::HTTP_FORBIDDEN);

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', Rule::enum(ServiceMonitorStatusSort::class)],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'timezone' => ['nullable', 'string', 'max:255'],
        ]);

        $search = $validated['search'] ?? null;
        $sort = ServiceMonitorStatusSort::tryFrom($validated['sort'] ?? '') ?? ServiceMonitorStatusSort::Name;
        $timezone = $validated['timezone'] ?? app(ResolvePortalDisplayTimezone::class)() ?? config('app.timezone');

        $uptimePeriods = [
            ServiceMonitorStatusSort::ThirtyDayUptime->value => 30,
            ServiceMonitorStatusSort::TwelveMonthUptime->value => 365,
        ];

        $sortsByUptime = in_array($sort, [ServiceMonitorStatusSort::ThirtyDayUptime, ServiceMonitorStatusSort::TwelveMonthUptime], true);

        $targets = ServiceMonitoringTarget::query()
            ->tap(new WithCurrentStatus())
            ->when($sortsByUptime, fn (Builder $query) => $query->tap(new WithUptimePercentages($uptimePeriods)))
            ->when(filled($search), function (Builder $query) use ($search): void {
                $pattern = '%' . addcslashes(Str::lower($search), '%_\\') . '%';

                $query->where(fn (Builder $query): Builder => $query
                    ->whereRaw('lower(service_monitoring_targets.name) like ?', [$pattern])
                    ->orWhereRaw('lower(service_monitoring_targets.description) like ?', [$pattern]));
            })
            ->tap(fn (Builder $query) => $sort->apply($query, $validated['direction'] ?? 'asc'))
            ->paginate(10);

        $targetIds = collect($targets->items())->map(fn (ServiceMonitoringTarget $target): string => $target->getKey());

        $uptimeByTargetId = match (true) {
            $sortsByUptime => collect($targets->items())->keyBy(fn (ServiceMonitoringTarget $target): string => $target->getKey()),
            $targetIds->isEmpty() => collect(),
            default => ServiceMonitoringTarget::query()
                ->select('service_monitoring_targets.id')
                ->tap(new WithUptimePercentages($uptimePeriods))
                ->whereIn('service_monitoring_targets.id', $targetIds)
                ->get()
                ->keyBy(fn (ServiceMonitoringTarget $target): string => $target->getKey()),
        };

        $history = $getStatusHistory($targetIds->all(), ServiceMonitoringHistoryPeriod::PastMonth, $timezone);

        $targets->through(function (ServiceMonitoringTarget $target) use ($uptimeByTargetId, $history): ServiceMonitorData {
            $uptime = $uptimeByTargetId->get($target->getKey());

            return new ServiceMonitorData(
                id: $target->getKey(),
                name: $target->name,
                description: $target->description,
                monitorType: $target->monitor_type,
                monitorTypeLabel: $target->monitor_type->getLabel(),
                frequencyLabel: $target->frequency->getLabel(),
                status: $target->getCurrentStatus(),
                lastCheckedAt: $target->getLastCheckedAt()?->toIso8601String(),
                thirtyDayUptimePercentage: $uptime?->getSelectedUptimePercentage(ServiceMonitorStatusSort::ThirtyDayUptime->value),
                twelveMonthUptimePercentage: $uptime?->getSelectedUptimePercentage(ServiceMonitorStatusSort::TwelveMonthUptime->value),
                history: $history[$target->getKey()] ?? [],
            );
        });

        return response()->json([
            'summary' => $this->summarize(),
            'data' => $targets->items(),
            'meta' => [
                'current_page' => $targets->currentPage(),
                'last_page' => $targets->lastPage(),
                'from' => $targets->firstItem() ?? 0,
                'to' => $targets->lastItem() ?? 0,
                'total' => $targets->total(),
                'per_page' => $targets->perPage(),
            ],
        ]);
    }

    /**
     * Summarizes every monitor the contact can see, regardless of any search.
     */
    protected function summarize(): ServiceMonitorSummaryData
    {
        $statusAggregates = DB::query()
            ->fromSub(
                ServiceMonitoringTarget::query()
                    ->select('service_monitoring_targets.id')
                    ->tap(new WithCurrentStatus()),
                'targets',
            )
            ->select('current_status')
            ->selectRaw('count(*) as targets_count')
            ->selectRaw('max(last_checked_at) as last_checked_at')
            ->groupBy('current_status')
            ->get()
            ->keyBy('current_status');

        $statusCounts = collect(ServiceMonitoringStatus::cases())
            ->mapWithKeys(fn (ServiceMonitoringStatus $status): array => [$status->value => (int) ($statusAggregates->get($status->value)->targets_count ?? 0)])
            ->all();

        $lastCheckedAt = $statusAggregates->max('last_checked_at');

        return new ServiceMonitorSummaryData(
            status: match (true) {
                ($statusCounts[ServiceMonitoringStatus::Outage->value] + $statusCounts[ServiceMonitoringStatus::Degraded->value]) > 0 => ServiceMonitoringStatus::Degraded,
                $statusCounts[ServiceMonitoringStatus::Operational->value] > 0 => ServiceMonitoringStatus::Operational,
                default => ServiceMonitoringStatus::Unknown,
            },
            totalCount: array_sum($statusCounts),
            statusCounts: $statusCounts,
            lastCheckedAt: is_string($lastCheckedAt) ? Carbon::parse($lastCheckedAt)->toIso8601String() : null,
        );
    }
}
