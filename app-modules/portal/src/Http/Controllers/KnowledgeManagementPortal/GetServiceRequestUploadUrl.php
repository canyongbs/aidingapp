<?php

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class GetServiceRequestUploadUrl extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'filename' => ['required', 'string'],
        ]);

        $filename = sprintf('%s.%s', Str::uuid(), str($validated['filename'])->afterLast('.'));
        $path = "service-requests-tmp/{$filename}";

        return response()->json([
            'filename' => $filename,
            'path' => $path,
            ...Storage::temporaryUploadUrl(
                $path,
                now()->addMinute(),
            ),
        ]);
    }
}
