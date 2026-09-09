<?php

namespace AidingApp\ServiceManagement\Models\Scopes;

use App\Features\ServiceRequestTypeGroupAssignmentsFeature;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AuditedServiceRequestTypes
{
    public function __construct(
        protected User $user,
    ) {}

    /** @param Builder<covariant Model> $query */
    public function __invoke(Builder $query): void
    {
        $query->where(function (Builder $query): void {
            $query
                ->whereHas('auditorUsers', fn (Builder $query) => $query->whereKey($this->user->getKey()))
                ->orWhereHas('auditorDepartments', fn (Builder $query) => $query->whereKey($this->user->department?->getKey()));

            if (ServiceRequestTypeGroupAssignmentsFeature::active()) {
                $query->orWhereHas(
                    'auditorGroups.users',
                    fn (Builder $query) => $query->whereKey($this->user->getKey()),
                );
            }
        });
    }
}
