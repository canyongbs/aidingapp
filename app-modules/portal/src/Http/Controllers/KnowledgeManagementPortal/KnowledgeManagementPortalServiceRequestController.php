<?php

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

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
