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

namespace App\Models\Concerns;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait TargetsRelationships
{
    public function targetingRelationship(string $state): bool
    {
        return Str::contains($state, '.');
    }

    /**
     *
     * @param Model $model
     * @param array<string> $relations
     *
     * @throws Exception
     *
     * @return Model|null
     *
     */
    public function accessNestedRelations(Model $model, array $relations): ?Model
    {
        $current = $model;

        foreach ($relations as $relation) {
            if (! method_exists($current, $relation)) {
                throw new Exception("Relation '{$relation}' does not exist on " . get_class($current));
            }

            // This is a workaround for if the relation is a soft-deleted model
            // This should be removed when we rework state machines to enforce minimum/maximum classification mappings
            $relatedClass = $current->{$relation}()->getRelated();

            if (method_exists($relatedClass, 'trashed')) {
                $current = $current->{$relation}()->withTrashed()->first();
            } else {
                $current = $current->{$relation};
            }

            if ($current === null) {
                return null;
            }
        }

        return $current;
    }

    /**
     *
     * @param Model $model
     * @param array<string> $methods
     *
     * @throws Exception
     *
     * @return Model|null
     *
     */
    public function dynamicMethodChain(Model $model, array $methods): ?Model
    {
        $current = $model;

        foreach ($methods as $method) {
            if (! method_exists($current, $method)) {
                throw new Exception("Method '{$method}' does not exist on " . get_class($current));
            }

            $current = $current->$method();
        }

        return $current;
    }
}
