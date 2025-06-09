<?php

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\ServiceManagement\Models\Incident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncidentController
{
    public function __invoke(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $paginated = Incident::with('severity')->orderBy('created_at', 'desc')->paginate($perPage);

        $grouped = $paginated->getCollection()->groupBy(function ($incident) {
            return $incident->created_at->format('F Y');
        });

        $paginated->setCollection($grouped);

        return response()->json(['data' => $paginated]);
    }
}
