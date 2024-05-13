<?php

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;

class GetKnowledgeManagementPortalTagsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(
            Tag::query()
                ->class(KnowledgeBaseItem::class)
                ->orderBy('name')
                ->get()
                ->select([
                    'id',
                    'name',
                ])
                ->toArray()
        );
    }
}
