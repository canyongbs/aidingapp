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
use AidingApp\Portal\DataTransferObjects\ServiceMonitorDetailData;
use AidingApp\ServiceManagement\Actions\GetServiceMonitoringStatusHistory;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringHistoryPeriod;
use AidingApp\ServiceManagement\Models\Scopes\WithCurrentStatus;
use AidingApp\ServiceManagement\Models\Scopes\WithUptimePercentages;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Http\Controllers\Controller;
use App\Settings\LicenseSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ShowServiceMonitorStatusController extends Controller
{
    /**
     * The number of days in each uptime period, from the shortest to the longest.
     *
     * @var array<string, int>
     */
    protected const array UPTIME_PERIODS = [
        'twenty_four_hours' => 1,
        'seven_days' => 7,
        'thirty_days' => 30,
        'ninety_days' => 90,
        'twelve_months' => 365,
    ];

    public function __invoke(Request $request, string $serviceMonitoringTarget, GetServiceMonitoringStatusHistory $getStatusHistory): JsonResponse
    {
        abort_unless(resolve(LicenseSettings::class)->data?->addons->serviceMonitoring, Response::HTTP_FORBIDDEN);

        $validated = $request->validate([
            'period' => ['nullable', Rule::enum(ServiceMonitoringHistoryPeriod::class)],
            'timezone' => ['nullable', 'string', 'max:255'],
        ]);

        $period = ServiceMonitoringHistoryPeriod::tryFrom($validated['period'] ?? '') ?? ServiceMonitoringHistoryPeriod::PastMonth;
        $timezone = $validated['timezone'] ?? app(ResolvePortalDisplayTimezone::class)() ?? config('app.timezone');

        $target = ServiceMonitoringTarget::query()
            ->tap(new WithCurrentStatus())
            ->tap(new WithUptimePercentages(self::UPTIME_PERIODS))
            ->findOrFail($serviceMonitoringTarget);

        $history = $getStatusHistory([$target->getKey()], $period, $timezone);

        return response()->json([
            'data' => new ServiceMonitorDetailData(
                id: $target->getKey(),
                name: $target->name,
                description: $target->description,
                domain: $target->domain,
                monitorType: $target->monitor_type,
                monitorTypeLabel: $target->monitor_type->getLabel(),
                frequencyLabel: $target->frequency->getLabel(),
                status: $target->getCurrentStatus(),
                lastCheckedAt: $target->getLastCheckedAt()?->toIso8601String(),
                uptimePercentages: collect(self::UPTIME_PERIODS)
                    ->map(fn (int $days, string $alias): ?float => $target->getSelectedUptimePercentage($alias))
                    ->all(),
                historyPeriod: $period,
                history: $history[$target->getKey()],
            ),
        ]);
    }
}
