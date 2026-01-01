<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Report\Filament\Widgets;

use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class ServiceRequestFeedbackStats extends StatsOverviewReportWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 4,
        'lg' => 4,
    ];

    /** @return list<string>|null */
    public function getServiceRequestTypes(): ?array
    {
        $types = $this->filters['serviceRequestTypes'] ?? null;

        return filled($types) ? (array) $types : null;
    }

    public function getStats(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $types = $this->getServiceRequestTypes();

        $shouldBypassCache = filled($startDate) || filled($endDate) || filled($types);

        $applyFilters = function (Builder $query, ?string $rootRelation = null) use ($startDate, $endDate, $types) {
            return $query
                ->when($startDate && $endDate, fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate]))
                ->when(
                    $types,
                    fn (Builder $query): Builder => $query->whereHas(
                        $rootRelation ? "{$rootRelation}.priority.type" : 'priority.type',
                        fn (Builder $relationQuery): Builder => $relationQuery->whereIn('id', $types)
                    )
                );
        };

        $serviceRequestsCount = $shouldBypassCache
            ? ServiceRequest::query()->tap(fn (Builder $query): Builder => $applyFilters($query))->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'total-service-requests-count',
                now()->addHours(24),
                fn () => ServiceRequest::query()->count()
            );

        $serviceRequestFeedbacksCount = $shouldBypassCache
            ? ServiceRequestFeedback::query()->tap(fn (Builder $query): Builder => $applyFilters($query, 'serviceRequest'))->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'total-service-request-feedbacks-count',
                now()->addHours(24),
                fn () => ServiceRequestFeedback::query()->count()
            );

        $csatAverage = $shouldBypassCache
            ? ServiceRequestFeedback::query()->tap(fn (Builder $query): Builder => $applyFilters($query, 'serviceRequest'))->avg('csat_answer')
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'csat-average',
                now()->addHours(24),
                fn () => ServiceRequestFeedback::query()->avg('csat_answer')
            );

        $npsAverage = $shouldBypassCache
            ? ServiceRequestFeedback::query()->tap(fn (Builder $query): Builder => $applyFilters($query, 'serviceRequest'))->avg('nps_answer')
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'nps-average',
                now()->addHours(24),
                fn () => ServiceRequestFeedback::query()->avg('nps_answer')
            );

        return [
            Stat::make('Tickets', Number::abbreviate($serviceRequestsCount, maxPrecision: 2)),
            Stat::make('Survey Responses', Number::abbreviate($serviceRequestFeedbacksCount, maxPrecision: 2)),
            Stat::make('CSAT Average', Number::abbreviate(round($csatAverage ?? 0, 2), maxPrecision: 2)),
            Stat::make('NPS Average', Number::abbreviate(round($npsAverage ?? 0, 2), maxPrecision: 2)),
        ];
    }
}
