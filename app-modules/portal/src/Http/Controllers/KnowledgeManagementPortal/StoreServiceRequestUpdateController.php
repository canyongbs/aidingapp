<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\Portal\Http\Requests\StoreServiceRequestUpdateRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class StoreServiceRequestUpdateController extends Controller
{
    public function __invoke(StoreServiceRequestUpdateRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $serviceRequestUpdate = new ServiceRequestUpdate();
            $serviceRequestUpdate->service_request_id = $request->serviceRequestId;
            $serviceRequestUpdate->update = $request->description;
            $serviceRequestUpdate->internal = false;

            $serviceRequestUpdate->createdBy()->associate($request->user(guard: 'contact'));

            $serviceRequestUpdate->save();

            if (! empty($request->input('files'))) {
                $this->processFileUploads($request->input('files'), $serviceRequestUpdate);
            }

            $serviceRequest = ServiceRequest::findOrFail($request->serviceRequestId);

            $serviceRequestUpdates = $serviceRequest
                ->serviceRequestUpdates()
                ->latest('created_at')
                ->where('internal', false)
                ->paginate(5)
                ->through(function (ServiceRequestUpdate $update) {
                    return [
                        'id' => $update->getKey(),
                        'update' => $update->update,
                        'created_by_type' => $update->created_by_type,
                        'created_at' => $update->created_at->format('M j, Y g:i a (T)'),
                        'media' => $update->getUploadedMedia(),
                    ];
                });

            return response()->json(['serviceRequestUpdates' => $serviceRequestUpdates], 201);
        });
    }

    /**
     * @param array<int, array{path: string, originalFileName: string}> $files
     */
    protected function processFileUploads(
        array $files,
        ServiceRequestUpdate $serviceRequestUpdate,
    ): void {
        foreach ($files as $file) {
            $path = $file['path'];
            $originalFileName = $file['originalFileName'];

            $this->validatePath($path);

            if (! Storage::exists($path)) {
                continue;
            }

            try {
                $serviceRequestUpdate
                    ->addMediaFromDisk($path)
                    ->usingName(pathinfo($originalFileName, PATHINFO_FILENAME))
                    ->createdBy($serviceRequestUpdate->createdBy)
                    ->toMediaCollection('uploads');
            } finally {
                Storage::delete($path);
            }
        }
    }

    protected function validatePath(string $path): void
    {
        if (str_contains($path, '..') || str_contains($path, '//')) {
            throw new InvalidArgumentException('Invalid path: path traversal not allowed');
        }

        if (! str_starts_with($path, 'tmp/')) {
            throw new InvalidArgumentException('Invalid path: must be within tmp/ directory');
        }

        if (! preg_match('/^tmp\/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\.[a-zA-Z0-9]+$/i', $path)) {
            throw new InvalidArgumentException('Invalid path: does not match expected format');
        }
    }
}
