<?php

namespace App\Models\Scopes;

use Laravel\Pennant\Feature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class SetupIsComplete implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (Feature::inactive('setup-complete')) {
            return;
        }

        $builder->where('setup_complete', true);
    }
}
