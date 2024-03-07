<?php

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagement;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class KnowledgeManagementPortalServiceRequestTypeController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
        ]);
    }
}
