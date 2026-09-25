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

use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ServiceMonitorStatusController extends Controller
{
    /**
     * Days shown per bar in the "history" sparkline included with each monitor.
     */
    private const int HISTORY_DAYS = 30;

    /**
     * @var array<string, int>
     */
    private const array FREQUENCY_MINUTES = [
        '5_minutes' => 5,
        '15_minutes' => 15,
        '30_minutes' => 30,
        '1_hour' => 60,
        '24_hours' => 1440,
    ];

    /**
     * @var array<string, int>
     */
    private const array STATUS_RANK = [
        'operational' => 0,
        'degraded' => 1,
        'outage' => 2,
        'unknown' => 3,
    ];

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $search = trim((string) $request->get('search', ''));
        $sort = (string) $request->get('sort', 'name');
        $direction = $request->get('direction') === 'desc' ? 'desc' : 'asc';
        $perPage = (int) $request->get('per_page', 15);
        $page = (int) $request->get('page', 1);

        $targets = ServiceMonitoringTarget::query()
            ->with('latestHistory')
            ->orderBy('name')
            ->get();

        $summary = $this->summarize($targets);

        $rows = $targets
            ->when(
                $search !== '',
                fn (Collection $targets) => $targets->filter(
                    fn (ServiceMonitoringTarget $target) => str_contains(Str::lower($target->name), Str::lower($search)),
                ),
            )
            ->map($this->present(...))
            ->values();

        $sorted = $this->sortRows($rows, $sort, $direction);

        $paginator = new LengthAwarePaginator(
            $sorted->forPage($page, $perPage)->values(),
            $sorted->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return response()->json([
            'summary' => $summary,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'path' => $paginator->path(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
                'first_page_url' => $paginator->url(1),
                'last_page_url' => $paginator->url($paginator->lastPage()),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
                'links' => $paginator->linkCollection(),
            ],
        ]);
    }

    /**
     * @param Collection<int, ServiceMonitoringTarget> $targets
     *
     * @return array{status: string, total: int, operational: int, degraded: int, outage: int, unknown: int}
     */
    private function summarize(Collection $targets): array
    {
        $counts = $targets
            ->map(fn (ServiceMonitoringTarget $target) => $this->status($target))
            ->countBy()
            ->all();

        $counts = [
            'operational' => $counts['operational'] ?? 0,
            'degraded' => $counts['degraded'] ?? 0,
            'outage' => $counts['outage'] ?? 0,
            'unknown' => $counts['unknown'] ?? 0,
        ];

        return [
            'status' => ($counts['outage'] > 0 || $counts['degraded'] > 0) ? 'degraded' : 'operational',
            'total' => $targets->count(),
            ...$counts,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function present(ServiceMonitoringTarget $target): array
    {
        $latestHistory = $target->latestHistory;

        if ($latestHistory) {
            $statusMessage = match ($latestHistory->succeeded) {
                true => 'No known issues at this time.',
                false => "Unable to reach service, status code: {$latestHistory->response}",
            };

            $latestHistoryArray = [
                ...$latestHistory->toArray(),
                'status_message' => $statusMessage,
            ];
        } else {
            $latestHistoryArray = null;
        }

        $thirtyDay = $target->getUptimePercentageValue(30);
        $twelveMonth = $target->getUptimePercentageValue(365);

        return [
            'id' => $target->id,
            'name' => $target->name,
            'domain' => $target->domain,
            'monitor_type' => $target->monitor_type,
            'frequency' => $target->frequency,
            'monitor_type_label' => $target->monitor_type->getLabel(),
            'frequency_label' => $target->frequency->getLabel(),
            'status' => $this->status($target),
            'latest_history' => $latestHistoryArray,
            'last_checked_at' => $latestHistory?->created_at,
            'last_checked_at_timestamp' => $latestHistory?->created_at->timestamp ?? -1,
            'uptime' => [
                'thirty_day' => ServiceMonitoringTarget::formatUptimePercentage($thirtyDay),
                'thirty_day_value' => $thirtyDay,
                'twelve_month' => ServiceMonitoringTarget::formatUptimePercentage($twelveMonth),
                'twelve_month_value' => $twelveMonth,
            ],
            'history' => $target->getDailyStatusHistory(self::HISTORY_DAYS),
        ];
    }

    private function status(ServiceMonitoringTarget $target): string
    {
        return match ($target->latestHistory?->succeeded) {
            true => 'operational',
            false => 'outage',
            default => 'unknown',
        };
    }

    /**
     * @param Collection<int, array<string, mixed>> $rows
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function sortRows(Collection $rows, string $sort, string $direction): Collection
    {
        $sorted = match ($sort) {
            'status' => $rows->sortBy(fn (array $row) => self::STATUS_RANK[$row['status']] ?? 99),
            'frequency' => $rows->sortBy(
                fn (array $row) => self::FREQUENCY_MINUTES[$row['frequency'] instanceof ServiceMonitoringFrequency ? $row['frequency']->value : $row['frequency']] ?? 0,
            ),
            'last_checked' => $rows->sortBy(fn (array $row) => $row['last_checked_at_timestamp']),
            'uptime_30_day' => $rows->sortBy(fn (array $row) => $row['uptime']['thirty_day_value'] ?? -1),
            'uptime_12_month' => $rows->sortBy(fn (array $row) => $row['uptime']['twelve_month_value'] ?? -1),
            default => $rows->sortBy(fn (array $row) => Str::lower($row['name'])),
        };

        return $direction === 'desc' ? $sorted->reverse()->values() : $sorted->values();
    }
}
