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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceMonitorStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);

        $paginated = ServiceMonitoringTarget::query()
            ->with(['latestHistory:id,response_time,succeeded,response,historical_service_monitorings.service_monitoring_target_id'])
            ->select('id', 'name', 'domain')
            ->orderBy('created_at', 'ASC')
            ->paginate($perPage);

        $paginated->through(function (ServiceMonitoringTarget $target) {
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

            return [
                ...$target->toArray(),
                'latest_history' => $latestHistoryArray,
            ];
        });

        return response()->json([
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'from' => $paginated->firstItem(),
                'last_page' => $paginated->lastPage(),
                'path' => $paginated->path(),
                'per_page' => $paginated->perPage(),
                'to' => $paginated->lastItem(),
                'total' => $paginated->total(),
                'first_page_url' => $paginated->url(1),
                'last_page_url' => $paginated->url($paginated->lastPage()),
                'next_page_url' => $paginated->nextPageUrl(),
                'prev_page_url' => $paginated->previousPageUrl(),
                'links' => $paginated->linkCollection(),
            ],
        ]);
    }
}
