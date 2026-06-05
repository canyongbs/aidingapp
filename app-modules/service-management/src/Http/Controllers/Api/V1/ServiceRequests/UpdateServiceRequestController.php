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

namespace AidingApp\ServiceManagement\Http\Controllers\Api\V1\ServiceRequests;

use AidingApp\ServiceManagement\Actions\UpdateServiceRequest;
use AidingApp\ServiceManagement\DataTransferObjects\UpdateServiceRequestData;
use AidingApp\ServiceManagement\Enums\ServiceRequestCategory;
use AidingApp\ServiceManagement\Http\Resources\Api\V1\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateServiceRequestController
{
    /**
     * @response ServiceRequestResource
     */
    #[Group('Service Requests')]
    public function __invoke(Request $request, UpdateServiceRequest $updateServiceRequest, ServiceRequest $serviceRequest): JsonResource
    {
        Gate::authorize('viewAny', ServiceRequest::class);
        Gate::authorize('update', $serviceRequest);

        $data = $request->validate([
            'status_id' => ['nullable', 'uuid:4', Rule::exists(ServiceRequestStatus::class, 'id')],
            'priority_id' => ['nullable', 'uuid:4', Rule::exists(ServiceRequestPriority::class, 'id')],
            'assigned_to_id' => ['nullable', 'uuid:4', Rule::exists(User::class, 'id')],
            'category' => ['nullable', 'string', 'max:255', Rule::in(ServiceRequestCategory::cases())],
            'close_details' => ['nullable', 'string'],
        ]);
        $serviceRequest = $updateServiceRequest->execute($serviceRequest, UpdateServiceRequestData::fromData($data));

        return $serviceRequest
            ->fresh(['status', 'priority', 'assignedTo.user', 'respondent'])
            ->toResource(ServiceRequestResource::class);
    }
}
