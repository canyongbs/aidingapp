<?php

namespace AidingApp\ServiceManagement\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AccessibleServiceRequests
{
    public function __construct(
        protected User $user,
    ) {}

    /** @param Builder<covariant Model> $query */
    public function __invoke(Builder $query): void
    {
        $query->whereHas(
            'priority.type',
            fn (Builder $query) => $query->tap(new AccessibleServiceRequestTypes($this->user)),
        );
    }
}
