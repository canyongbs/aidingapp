<?php

namespace App\Http\Controllers;

use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\ServiceManagement\Models\ChangeRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\Task\Models\Task;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UtilizationMetricsApisController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $metrics = [
                'users' => User::count(),
                'service_requests' => ServiceRequest::count(),
                'assets' => Asset::count(),
                'changes' => ChangeRequest::count(),
                'knowledge_base_articles' => KnowledgeBaseItem::count(),
                'tasks' => Task::count()
            ];

            return response()->json([
                'data' => $metrics,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
            ], 500);
        }
    }
}
