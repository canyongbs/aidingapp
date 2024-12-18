<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\Scopes\ClassifiedIn;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ServiceRequestsStats extends StatsOverviewReportWidget
{
    protected int $daysAgo = 30;

    protected function getStats(): array
    {
        $intervalStart = now()->subDays($this->daysAgo);

        [$currentAllServiceRequests, $allServiceRequestsPercentageChange, $allServiceRequestsIcon, $allServiceRequestsColor] = Cache::tags([$this->cacheTag])->remember('all-service-requests_count', now()->addHours(24), function () use ($intervalStart): array {
            return $this->calculateAllServiceRequestStats($intervalStart);
        });

        [$currentOpenServiceRequests, $openServiceRequestsPercentageChange, $openServiceRequestsIcon, $openServiceRequestsColor] = Cache::tags([$this->cacheTag])->remember('open-service-requests_count', now()->addHours(24), function () use ($intervalStart): array {
            return $this->calculateOpenServiceRequestStats($intervalStart);
        });

        [$averageServiceResolutionTime, $averageServiceResolutionTimePercentageChange, $averageServiceResolutionTimeIcon, $averageServiceResolutionTimeColor] = Cache::tags([$this->cacheTag])->remember('average-service-resolution-time', now()->addHours(24), function () use ($intervalStart): array {
            return $this->calculateAverageServiceResolutionTime($intervalStart);
        });

        return [
            Stat::make('Total Service Requests', $currentAllServiceRequests)
                ->description($allServiceRequestsPercentageChange)
                ->descriptionIcon($allServiceRequestsIcon)
                ->color($allServiceRequestsColor),
            Stat::make('Average Resolution Time', $averageServiceResolutionTime)
                ->description($averageServiceResolutionTimePercentageChange)
                ->descriptionIcon($averageServiceResolutionTimeIcon)
                ->color($averageServiceResolutionTimeColor),
            Stat::make('Total Open Requests', $currentOpenServiceRequests)
                ->description($openServiceRequestsPercentageChange)
                ->descriptionIcon($openServiceRequestsIcon)
                ->color($openServiceRequestsColor),
        ];
    }

    private function calculateOpenServiceRequestStats(Carbon $intervalStart): array
    {
        $openStatusIds = ServiceRequestStatus::tap(new ClassifiedIn(SystemServiceRequestClassification::getUnclosedClassifications()))->pluck('id');

        $currentOpenServiceRequests = ServiceRequest::whereIn('status_id', $openStatusIds)->count();

        $serviceRequestsCreatedBeforeIntervalStart = ServiceRequest::query()
            ->with(['histories' => function ($query) use ($intervalStart) {
                $query->whereBetween('created_at', [$intervalStart, now()])
                    ->whereRaw("original_values->>'status_id' IS NOT NULL")  // Checks if status_id was actually changed
                    ->orderBy('created_at', 'asc')
                    ->limit(1);
            }])
            ->where('created_at', '<=', $intervalStart)->get();

        $openServiceRequestsAtIntervalCount = $serviceRequestsCreatedBeforeIntervalStart->filter(function (ServiceRequest $serviceRequest) use ($openStatusIds) {
            return $this->wasOpenAtIntervalStart($serviceRequest, $openStatusIds);
        })->count();

        $percentageChange = $this->getPercentageChange($openServiceRequestsAtIntervalCount, $currentOpenServiceRequests);

        [$percentageChangeDisplayValue, $icon, $color] = $this->getFormattedPercentageChangeDetails($percentageChange);

        return [
            $currentOpenServiceRequests,
            $percentageChangeDisplayValue,
            $icon,
            $color,
        ];
    }

    private function wasOpenAtIntervalStart(ServiceRequest $serviceRequest, $openStatusIds): bool
    {
        // If the service request has no history and it is open, it was open at interval date
        if ($serviceRequest->histories->isEmpty()) {
            return $openStatusIds->contains($serviceRequest->status_id);
        }

        // If the service requests first history after the interval was a change from open to something else, it was open at interval date
        if ($history = $serviceRequest->histories->first()) {
            $originalStatusId = $history->original_values['status_id'] ?? null;

            return $openStatusIds->contains($originalStatusId);
        }

        return false;
    }

    private function calculateAllServiceRequestStats(Carbon $intervalStart): array
    {
        $currentAllServiceRequests = ServiceRequest::count();

        $allServiceRequestsAtIntervalCount = ServiceRequest::query()
            ->where('created_at', '<=', $intervalStart)
            ->count();

        $percentageChange = $this->getPercentageChange($allServiceRequestsAtIntervalCount, $currentAllServiceRequests);

        [$percentageChangeDisplayValue, $icon, $color] = $this->getFormattedPercentageChangeDetails($percentageChange);

        return [
            $currentAllServiceRequests,
            $percentageChangeDisplayValue,
            $icon,
            $color,
        ];
    }

    private function calculateAverageServiceResolutionTime(Carbon $intervalStart): array
    {
        $averageServiceResolutionTime = ServiceRequest::avg('time_to_resolution');
        $interval = Carbon::now()->diffAsCarbonInterval(Carbon::now()->addSeconds($averageServiceResolutionTime));
        $days = $interval->d;
        $hours = $interval->h;
        $minutes = $interval->i;
        $averageServiceResolutionTimeAtIntervalCount = ServiceRequest::query()
            ->where('created_at', '<=', $intervalStart)
            ->avg('time_to_resolution');

        $percentageChange = $this->getPercentageChange((int) $averageServiceResolutionTimeAtIntervalCount, (int) $averageServiceResolutionTime);

        [$percentageChangeDisplayValue, $icon, $color] = $this->getFormattedPercentageChangeDetails($percentageChange);

        $averageServiceResolutionTime = "{$days}d {$hours}h {$minutes}m";

        return [
            $averageServiceResolutionTime,
            $percentageChangeDisplayValue,
            $icon,
            $color,
        ];
    }

    private function getPercentageChange(int $oldValue, int $newValue): int
    {
        return $oldValue > 0
                        ? (($newValue - $oldValue) / $oldValue) * 100
                        : ($newValue > 0 ? 100 : 0);
    }

    private function getFormattedPercentageChangeDetails(int $percentageChange): array
    {
        if ($percentageChange > 0) {
            $percentageChangeDisplayValue = number_format($percentageChange) . '% increase';
            $icon = 'heroicon-m-arrow-trending-up';
            $color = 'success';
        } elseif ($percentageChange < 0) {
            $percentageChangeDisplayValue = number_format($percentageChange * -1) . '% decrease';
            $icon = 'heroicon-m-arrow-trending-down';
            $color = 'danger';
        } else {
            $percentageChangeDisplayValue = 'No change';
            $icon = null;
            $color = null;
        }

        return [
            $percentageChangeDisplayValue,
            $icon,
            $color,
        ];
    }
}
