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
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            // Not validated as a strict IANA identifier: browsers can report legacy aliases PHP doesn't
            // recognize by name (e.g. `Asia/Calcutta`), and `GetServiceMonitoringStatusHistory` resolves those
            // gracefully rather than rejecting the request over a display preference.
            'timezone' => ['nullable', 'string', 'max:255'],
        ]);

        $search = $validated['search'] ?? null;
        $sort = ServiceMonitorStatusSort::tryFrom($validated['sort'] ?? '') ?? ServiceMonitorStatusSort::Name;
        $timezone = $validated['timezone'] ?? app(ResolvePortalDisplayTimezone::class)() ?? config('app.timezone');

        $targets = ServiceMonitoringTarget::query()
            ->tap(new WithCurrentStatus())
            ->tap(new WithUptimePercentages([
                ServiceMonitorStatusSort::ThirtyDayUptime->value => 30,
                ServiceMonitorStatusSort::TwelveMonthUptime->value => 365,
            ]))
            ->when(filled($search), function (Builder $query) use ($search): void {
                $pattern = '%' . addcslashes($search, '%_\\') . '%';

                $query->where(fn (Builder $query): Builder => $query
                    ->where('service_monitoring_targets.name', 'ilike', $pattern)
                    ->orWhere('service_monitoring_targets.description', 'ilike', $pattern));
            })
            ->tap(fn (Builder $query) => $sort->apply($query, $validated['direction'] ?? 'asc'))
            ->paginate(10);

        $history = $getStatusHistory(
            collect($targets->items())->map(fn (ServiceMonitoringTarget $target): string => $target->getKey())->all(),
            ServiceMonitoringHistoryPeriod::PastMonth,
            $timezone,
        );

        $targets->through(fn (ServiceMonitoringTarget $target): ServiceMonitorData => new ServiceMonitorData(
            id: $target->getKey(),
            name: $target->name,
            description: $target->description,
            monitorType: $target->monitor_type,
            monitorTypeLabel: $target->monitor_type->getLabel(),
            frequencyLabel: $target->frequency->getLabel(),
            status: $target->getCurrentStatus(),
            statusLabel: $target->getCurrentStatus()->getLabel(),
            lastCheckedAt: $target->getLastCheckedAt()?->toIso8601String(),
            thirtyDayUptimePercentage: $target->getSelectedUptimePercentage(ServiceMonitorStatusSort::ThirtyDayUptime->value),
            twelveMonthUptimePercentage: $target->getSelectedUptimePercentage(ServiceMonitorStatusSort::TwelveMonthUptime->value),
            history: $history[$target->getKey()] ?? [],
        ));

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
        $targets = ServiceMonitoringTarget::query()
            ->select('service_monitoring_targets.id')
            ->tap(new WithCurrentStatus())
            ->get();

        $statusCounts = [
            ...collect(ServiceMonitoringStatus::cases())->mapWithKeys(fn (ServiceMonitoringStatus $status): array => [$status->value => 0]),
            ...$targets->countBy(fn (ServiceMonitoringTarget $target): string => $target->getCurrentStatus()->value),
        ];

        $lastCheckedAt = $targets->map(fn (ServiceMonitoringTarget $target): ?CarbonInterface => $target->getLastCheckedAt())->max();

        return new ServiceMonitorSummaryData(
            status: match (true) {
                ($statusCounts[ServiceMonitoringStatus::Outage->value] + $statusCounts[ServiceMonitoringStatus::Degraded->value]) > 0 => ServiceMonitoringStatus::Degraded,
                $statusCounts[ServiceMonitoringStatus::Operational->value] > 0 => ServiceMonitoringStatus::Operational,
                default => ServiceMonitoringStatus::Unknown,
            },
            totalCount: $targets->count(),
            statusCounts: $statusCounts,
            lastCheckedAt: $lastCheckedAt instanceof CarbonInterface ? $lastCheckedAt->toIso8601String() : null,
        );
    }
}
