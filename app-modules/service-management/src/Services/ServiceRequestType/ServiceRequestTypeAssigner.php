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

namespace AidingApp\ServiceManagement\Services\ServiceRequestType;

use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Models\ServiceRequest;

use App\Models\User;
use Illuminate\Support\Facades\DB;

abstract class ServiceRequestTypeAssigner
{
    final public function execute(ServiceRequest $serviceRequest): void
    {
        $user = $this->resolveAssignee($serviceRequest);

        if ($user !== null) {
            $this->assign($serviceRequest, $user);
        }
    }

    abstract protected function resolveAssignee(ServiceRequest $serviceRequest): ?User;

    private function assign(ServiceRequest $serviceRequest, User $user): void
    {
        DB::transaction(function () use ($serviceRequest, $user): void {
            $serviceRequest->assignments()->create([
                'user_id' => $user->getKey(),
                'assigned_by_id' => null,
                'assigned_by_type' => null,
                'assigned_at' => now(),
                'status' => ServiceRequestAssignmentStatus::Active,
            ]);

            $this->applyAutomatedStatusChange($serviceRequest);
        });
    }

    private function applyAutomatedStatusChange(ServiceRequest $serviceRequest): void
    {
        $type = $serviceRequest->priority?->type;

        if (blank($type?->automated_status_id)) {
            return;
        }

        if ($serviceRequest->status_id === $type->automated_status_id) {
            return;
        }

        $serviceRequest->status()->associate($type->automated_status_id);
        $serviceRequest->save();
    }
}
