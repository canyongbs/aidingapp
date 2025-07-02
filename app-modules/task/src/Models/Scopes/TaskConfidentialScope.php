<?php

namespace AidingApp\Task\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TaskConfidentialScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->user()->isSuperAdmin()) {
            return;
        }

        $builder->where('is_confidential', false)->orWhere(function (Builder $query) {
            $query->where('is_confidential', true)
                ->where(function (Builder $query) {
                    $query->where('created_by', auth()->id())
                        ->orWhereHas('confidentialAccessTeams', function (Builder $query) {
                            $query->whereHas('users', function (Builder $query) {
                                $query->where('users.id', auth()->id());
                            });
                        })
                        ->orWhereHas('confidentialAccessUsers', function (Builder $query) {
                            $query->where('user_id', auth()->id());
                        });
                });
        });
    }
}
