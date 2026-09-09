<?php

namespace AidingApp\ServiceManagement\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AccessibleServiceRequestTypes
{
    public function __construct(
        protected User $user,
    ) {}

    /** @param Builder<covariant Model> $query */
    public function __invoke(Builder $query): void
    {
        $query->where(function (Builder $query): void {
            $query
                ->where(fn (Builder $query) => $query->tap(new ManagedServiceRequestTypes($this->user)))
                ->orWhere(fn (Builder $query) => $query->tap(new AuditedServiceRequestTypes($this->user)));
        });
    }
}
