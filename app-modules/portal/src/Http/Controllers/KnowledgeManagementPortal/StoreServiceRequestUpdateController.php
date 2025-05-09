<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\Portal\Http\Requests\StoreServiceRequestUpdateRequest;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class StoreServiceRequestUpdateController extends Controller
{
    public function __invoke(StoreServiceRequestUpdateRequest $request): JsonResponse
    {
        $serviceRequestUpdate = new ServiceRequestUpdate();
        $serviceRequestUpdate->service_request_id = $request->serviceRequestId;
        $serviceRequestUpdate->update = $request->description;
        $serviceRequestUpdate->internal = false;
        $serviceRequestUpdate->direction = ServiceRequestUpdateDirection::Inbound;
        $serviceRequestUpdate->save();

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
                    'direction' => $update->direction,
                    'created_at' => $update->created_at->format('m-d-Y g:i A'),
                ];
            });

        return response()->json(['serviceRequestUpdates' => $serviceRequestUpdates], 201);
    }
}
