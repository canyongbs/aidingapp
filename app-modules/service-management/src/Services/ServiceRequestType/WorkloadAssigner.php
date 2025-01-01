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

namespace AidingApp\ServiceManagement\Services\ServiceRequestType;

use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder;

class WorkloadAssigner implements ServiceRequestTypeAssigner
{
    public function execute(ServiceRequest $serviceRequest): void
    {
        $serviceRequestType = $serviceRequest->priority->type;

        if (! is_null($serviceRequestType)) {
            $lastAssignee = $serviceRequestType->lastAssignedUser;
            $user = null;

            if ($lastAssignee) {
                $lowestServiceRequest = User::query()->whereRelation('teams.managableServiceRequestTypes', 'service_request_types.id', $serviceRequestType->getKey())
                    ->withCount([
                        'serviceRequests as service_request_count' => function (Builder $query) {
                            $query->whereRelation('status', 'classification', '!=', SystemServiceRequestClassification::Closed);
                        },
                    ])
                    ->orderBy('service_request_count', 'asc')
                    ->first()?->service_request_count ?? 0;

                $user = User::query()->whereRelation('teams.managableServiceRequestTypes', 'service_request_types.id', $serviceRequestType->getKey())
                    ->where(function (QueryBuilder $query) {
                        $query->selectRaw('count(*)')
                            ->from('service_requests')
                            ->Join('service_request_assignments', 'service_request_assignments.service_request_id', '=', 'service_requests.id')
                            ->whereColumn('users.id', 'service_request_assignments.user_id')
                            ->whereExists(function (QueryBuilder $query) {
                                $query->selectRaw('*')
                                    ->from('service_request_statuses')
                                    ->whereColumn('service_requests.status_id', 'service_request_statuses.id')
                                    ->where('classification', '!=', SystemServiceRequestClassification::Closed)
                                    ->whereNull('service_request_statuses.deleted_at');
                            })
                            ->whereNull('service_requests.deleted_at')
                            ->whereNull('service_request_assignments.deleted_at');
                    }, '<=', $lowestServiceRequest)
                    ->where('name', '>=', $lastAssignee->name)
                    ->where(fn (Builder $query) => $query
                        ->where('name', '!=', $lastAssignee->name)
                        ->orWhere('users.id', '>', $lastAssignee->id))
                    ->orderBy('name')->orderBy('id')->first();
            }

            if ($user === null) {
                $user = User::query()->whereRelation('teams.managableServiceRequestTypes', 'service_request_types.id', $serviceRequestType->getKey())
                    ->withCount([
                        'serviceRequests as service_request_count' => function (Builder $query) {
                            $query->whereRelation('status', 'classification', '!=', SystemServiceRequestClassification::Closed);
                        },
                    ])
                    ->orderBy('service_request_count', 'asc')
                    ->orderBy('name')->orderBy('id')->first();
            }

            if ($user !== null) {
                $serviceRequestType->last_assigned_id = $user->getKey();
                $serviceRequestType->save();
                $serviceRequest->assignments()->create([
                    'user_id' => $user->getKey(),
                    'assigned_by_id' => auth()->user() ? auth()->user()->getKey() : null,
                    'assigned_at' => now(),
                    'status' => ServiceRequestAssignmentStatus::Active,
                ]);
            }
        }
    }
}
