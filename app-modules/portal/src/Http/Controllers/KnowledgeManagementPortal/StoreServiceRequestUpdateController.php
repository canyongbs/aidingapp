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
use AidingApp\Portal\Jobs\PersistServiceRequestUpdateUpload;
use AidingApp\ServiceManagement\Models\MediaCollections\UploadsMediaCollection;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;

class StoreServiceRequestUpdateController extends Controller
{
    public function __invoke(StoreServiceRequestUpdateRequest $request): JsonResponse
    {
        $serviceRequestUpdate = new ServiceRequestUpdate();
        $serviceRequestUpdate->service_request_id = $request->serviceRequestId;
        $serviceRequestUpdate->update = $request->description;
        $serviceRequestUpdate->internal = false;

        $serviceRequestUpdate->createdBy()->associate($request->user(guard: 'contact'));

        $serviceRequestUpdate->save();

        if (! empty($request->input('files'))) {
            $this->dispatchFileUploads(collect($request->all()), $serviceRequestUpdate, new UploadsMediaCollection('uploads'));
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
                    'created_at' => $update->created_at->format('m-d-Y g:i A'),
                    'media' => $update->getUploadedMedia(),
                ];
            });

        return response()->json(['serviceRequestUpdates' => $serviceRequestUpdates], 201);
    }

    /**
     * @param Collection<string, mixed> $data
     */
    protected function dispatchFileUploads(
        Collection $data,
        ServiceRequestUpdate $serviceRequestUpdate,
        UploadsMediaCollection $uploadsMediaCollection
    ): void {
        /** @var array<int, array{path: string, originalFileName: string}> $filesData */
        $filesData = $data->pull('files', []);
        $files = collect($filesData);

        if (empty($files)) {
            return;
        }

        Bus::batch([
            ...$files->map(function (array $file) use ($uploadsMediaCollection, $serviceRequestUpdate) {
                return new PersistServiceRequestUpdateUpload(
                    $serviceRequestUpdate,
                    $file['path'],
                    $file['originalFileName'],
                    $uploadsMediaCollection->getName(),
                );
            }),
        ])
            ->name("persist-service-request-uploads-{$serviceRequestUpdate->getKey()}")
            ->dispatchAfterResponse();
    }
}
