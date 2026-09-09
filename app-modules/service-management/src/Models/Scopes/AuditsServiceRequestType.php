<?php

namespace AidingApp\ServiceManagement\Models\Scopes;

use AidingApp\ServiceManagement\Models\ServiceRequestTypeAuditorGroup;
use App\Features\ServiceRequestTypeGroupAssignmentsFeature;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AuditsServiceRequestType
{
    public function __construct(
        protected string $serviceRequestTypeId,
    ) {}

    /** @param Builder<covariant Model> $query */
    public function __invoke(Builder $query): void
    {
        $query->where(function (Builder $query): void {
            $query
                ->whereHas('department.auditableServiceRequestTypes', function (Builder $query): void {
                    $query->where('service_request_type_id', $this->serviceRequestTypeId);
                })
                ->orWhereHas('auditableServiceRequestTypes', function (Builder $query): void {
                    $query->where('service_request_type_id', $this->serviceRequestTypeId);
                });

            if (ServiceRequestTypeGroupAssignmentsFeature::active()) {
                $query->orWhereHas('groups', function (Builder $query): void {
                    $query->whereIn(
                        'groups.id',
                        ServiceRequestTypeAuditorGroup::query()
                            ->select('group_id')
                            ->where('service_request_type_id', $this->serviceRequestTypeId),
                    );
                });
            }
        });
    }
}
