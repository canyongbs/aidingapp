<?php

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
                    true => 'No known issues at this time',
                    false => '1 unknown issue reported',
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
