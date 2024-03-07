<?php

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

class ServiceRequestTypesController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'types' => ServiceRequestType::query()
                // TODO We might not ultimately want to restrict this to only types with forms
                // But it will require some refactoring to support non-form-having service request types
                ->whereHas('form')
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
