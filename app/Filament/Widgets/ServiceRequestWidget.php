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

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Filament\Widgets\StatsOverviewWidget\Stat;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use AidingApp\ServiceManagement\Models\Scopes\ClassifiedAs;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;

class ServiceRequestWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected int $daysAgo = 30;

    protected int $secondsToCache = 172800; // 48 hours

    public function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $intervalStart = now()->subDays($this->daysAgo);

        [$currentOpenServiceRequests, $openServiceRequestsPercentageChange, $openServiceRequestsIcon, $openServiceRequestsColor] = $this->calculateOpenServiceRequestStats($intervalStart);

        [$currentUnassignedServiceRequests, $unassignedServiceRequestsPercentageChange, $unassignedServiceRequestsIcon, $unassignedServiceRequestsColor] = $this->calculateUnassignedServiceRequestStats($intervalStart);

        return [
            Stat::make('Open Service Requests', $currentOpenServiceRequests)
                ->description($openServiceRequestsPercentageChange)
                ->descriptionIcon($openServiceRequestsIcon)
                ->color($openServiceRequestsColor),
            Stat::make('Unassigned Service Requests', $currentUnassignedServiceRequests)
                ->description($unassignedServiceRequestsPercentageChange)
                ->descriptionIcon($unassignedServiceRequestsIcon)
                ->color($unassignedServiceRequestsColor),
        ];
    }

    private function calculateOpenServiceRequestStats(Carbon $intervalStart): array
    {
        $openStatusIds = ServiceRequestStatus::tap(new ClassifiedAs(SystemServiceRequestClassification::Open))->pluck('id');

        $currentOpenServiceRequests = ServiceRequest::whereIn('status_id', $openStatusIds)->count();

        $serviceRequestsCreatedBeforeIntervalStart = ServiceRequest::query()
            ->with(['histories' => function ($query) use ($intervalStart) {
                $query->whereBetween('created_at', [$intervalStart, now()])
                    ->whereRaw("original_values->>'status_id' IS NOT NULL")  // Checks if status_id was actually changed
                    ->orderBy('created_at', 'asc')
                    ->limit(1);
            }])
            ->where('created_at', '<=', $intervalStart)->get();

        $openServiceRequestsAtIntervalCount = Cache::remember("open_service_requests_{$intervalStart->year}_{$intervalStart->month}_{$intervalStart->day}", $this->secondsToCache, function () use ($serviceRequestsCreatedBeforeIntervalStart, $openStatusIds) {
            return $serviceRequestsCreatedBeforeIntervalStart->filter(function (ServiceRequest $serviceRequest) use ($openStatusIds) {
                // If the service request is open, and doesn't have any history, it was open at interval date
                if ($openStatusIds->contains($serviceRequest->status_id) && $serviceRequest->histories->isEmpty()) {
                    return true;
                }

                // If the service request is not open, and doesn't have any history, it was not open at interval date
                if ($openStatusIds->doesntContain($serviceRequest->status_id) && $serviceRequest->histories->isEmpty()) {
                    return false;
                }

                // If the service requests history has a status_id that is in the open status ids, it was open at interval date
                if ($history = $serviceRequest->histories->first()) {
                    $originalStatusId = $history->original_values['status_id'] ?? null;

                    return $openStatusIds->contains($originalStatusId);
                }

                return false;
            })->count();
        });

        $percentageChange = $this->getPercentageChange($openServiceRequestsAtIntervalCount, $currentOpenServiceRequests);

        [$percentageChange, $icon, $color] = $this->getFormattedPercentageChangeDetails($percentageChange);

        return [
            $currentOpenServiceRequests,
            $percentageChange,
            $icon,
            $color,
        ];
    }

    private function calculateUnassignedServiceRequestStats(Carbon $intervalStart): array
    {
        $currentUnassignedServiceRequests = ServiceRequest::doesntHave('assignments')->count();

        $unassignedServiceRequestsAtIntervalCount = Cache::remember("unassigned_service_requests_{$intervalStart->year}_{$intervalStart->month}_{$intervalStart->day}", $this->secondsToCache, function () use ($intervalStart) {
            return ServiceRequest::query()
                ->whereDoesntHave('assignments', function ($query) use ($intervalStart) {
                    $query->where('assigned_at', '<=', $intervalStart);
                })
                ->where('created_at', '<=', $intervalStart)
                ->count();
        });

        $percentageChange = $this->getPercentageChange($unassignedServiceRequestsAtIntervalCount, $currentUnassignedServiceRequests);

        [$percentageChange, $icon, $color] = $this->getFormattedPercentageChangeDetails($percentageChange);

        return [
            $currentUnassignedServiceRequests,
            $percentageChange,
            $icon,
            $color,
        ];
    }

    private function getPercentageChange($oldValue, $newValue): int
    {
        return $oldValue > 0
                        ? (($newValue - $oldValue) / $oldValue) * 100
                        : ($newValue > 0 ? 100 : 0);
    }

    private function getFormattedPercentageChangeDetails(int $percentageChange): array
    {
        if ($percentageChange > 0) {
            $percentageChange = number_format($percentageChange) . '% increase';
            $icon = 'heroicon-m-arrow-trending-up';
            $color = 'success';
        } elseif ($percentageChange < 0) {
            $percentageChange = number_format($percentageChange * -1) . '% decrease';
            $icon = 'heroicon-m-arrow-trending-down';
            $color = 'danger';
        } else {
            $percentageChange = 'No change';
            $icon = null;
            $color = null;
        }

        return [
            $percentageChange,
            $icon,
            $color,
        ];
    }
}
