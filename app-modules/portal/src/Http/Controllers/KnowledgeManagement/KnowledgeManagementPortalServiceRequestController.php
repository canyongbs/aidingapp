<?php

namespace AdvisingApp\Portal\Http\Controllers\KnowledgeManagement;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use AdvisingApp\ServiceManagement\Models\ServiceRequestType;

class KnowledgeManagementPortalServiceRequestController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'types' => ServiceRequestType::query()
                ->orderBy('name')
                ->get()
                ->map(function (ServiceRequestType $type) {
                    return [
                        'id' => $type->getKey(),
                        'name' => $type->name,
                        'description' => $type->description,
                        'icon' => $type->icon ? svg($type->icon, 'h-6 w-6')->toHtml() : null,
                    ];
                }),
        ]);
    }
}
