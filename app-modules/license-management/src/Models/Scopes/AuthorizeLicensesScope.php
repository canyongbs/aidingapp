<?php

namespace AidingApp\LicenseManagement\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class AuthorizeLicensesScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->user()?->isSuperAdmin()) {
            return;
        }

        $builder->where('created_by_id', auth()->id());
    }
}
