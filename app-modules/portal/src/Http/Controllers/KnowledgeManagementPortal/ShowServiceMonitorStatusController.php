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

use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ShowServiceMonitorStatusController extends Controller
{
    /**
     * Days shown per bar in the "history" sparkline.
     */
    private const int HISTORY_DAYS = 30;

    /**
     * Uptime periods (in days) shown on the detail page, keyed by their display label.
     *
     * @var array<string, int>
     */
    private const array UPTIME_PERIODS = [
        'twenty_four_hour' => 1,
        'seven_day' => 7,
        'thirty_day' => 30,
        'ninety_day' => 90,
        'twelve_month' => 365,
    ];

    /**
     * Handle the incoming request. Visibility of confidential monitors is enforced by the
     * model's global ServiceMonitoringTargetVisibilityScope, applied on route-model binding.
     */
    public function __invoke(ServiceMonitoringTarget $serviceMonitoringTarget): JsonResponse
    {
        $latestHistory = $serviceMonitoringTarget->latestHistory;

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

        $uptime = collect(self::UPTIME_PERIODS)
            ->mapWithKeys(function (int $days, string $key) use ($serviceMonitoringTarget) {
                $value = $serviceMonitoringTarget->getUptimePercentageValue($days);

                return [
                    $key => [
                        'value' => $value,
                        'label' => ServiceMonitoringTarget::formatUptimePercentage($value),
                    ],
                ];
            });

        return response()->json([
            'data' => [
                'id' => $serviceMonitoringTarget->id,
                'name' => $serviceMonitoringTarget->name,
                'domain' => $serviceMonitoringTarget->domain,
                'monitor_type' => $serviceMonitoringTarget->monitor_type,
                'monitor_type_label' => $serviceMonitoringTarget->monitor_type->getLabel(),
                'frequency' => $serviceMonitoringTarget->frequency,
                'frequency_label' => $serviceMonitoringTarget->frequency->getLabel(),
                'status' => match ($latestHistory?->succeeded) {
                    true => 'operational',
                    false => 'outage',
                    default => 'unknown',
                },
                'latest_history' => $latestHistoryArray,
                'last_checked_at' => $latestHistory?->created_at,
                'uptime' => $uptime,
                'history' => $serviceMonitoringTarget->getDailyStatusHistory(self::HISTORY_DAYS),
            ],
        ]);
    }
}
