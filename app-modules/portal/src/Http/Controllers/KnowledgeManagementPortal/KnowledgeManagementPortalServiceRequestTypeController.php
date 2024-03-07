<?php

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use AidingApp\Form\Actions\GenerateFormKitSchema;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

class KnowledgeManagementPortalServiceRequestTypeController extends Controller
{
    public function show(GenerateFormKitSchema $generateSchema, ServiceRequestType $type): JsonResponse
    {
        // We need to provide the same information back to the frontend that the form would
        // And then we need to render it in the exact same capacity.
        return response()->json([
            'schema' => $generateSchema($type->form),
        ]);
    }
}
