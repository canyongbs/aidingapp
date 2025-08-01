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

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class FilterQueryBuilder
{
    protected Model $model;

    protected string $table;

    /**
     * @param Builder<Model> $query
     * @param array<string, mixed> $data
     *
     * @return Builder<Model>
     */
    public function apply(Builder $query, array $data): Builder
    {
        $this->model = $query->getModel();
        $this->table = $this->model->getTable();

        if (isset($data['f'])) {
            foreach ($data['f'] as $filter) {
                $filter['match'] = $data['filter_match'] ?? 'and';
                $this->makeFilter($query, $filter);
            }
        }

        $this->makeOrder($query, $data);

        return $query;
    }

    /**
     * @param array<string, mixed> $filter
     * @param Builder<Model> $query
     *
     * @return Builder<Model>
     */
    public function contains($filter, $query): Builder
    {
        $filter['query_1'] = addslashes($filter['query_1']);

        return $query->where($filter['column'], 'like', '%' . $filter['query_1'] . '%', $filter['match']);
    }

    /**
     * @param Builder<Model> $query
     * @param array<string, mixed> $data
     */
    protected function makeOrder(Builder $query, array $data): void
    {
        if ($this->isNestedColumn($data['order_column'])) {
            [$relationship, $column] = explode('.', $data['order_column']);
            $callable = Str::camel($relationship);
            $belongs = $this->model->{$callable}(
            );
            $relatedModel = $belongs->getModel();
            $relatedTable = $relatedModel->getTable();
            $as = "prefix_{$relatedTable}";

            if (! $belongs instanceof BelongsTo) {
                return;
            }

            $query->leftJoin(
                "{$relatedTable} as {$as}",
                "{$as}.id",
                '=',
                "{$this->table}.{$relationship}_id"
            );

            $data['order_column'] = "{$as}.{$column}";
        }

        $query
            ->orderBy($data['order_column'], $data['order_direction'])
            ->select("{$this->table}.*");
    }

    /**
     * @param Builder<Model> $query
     * @param array<string, mixed> $filter
     */
    protected function makeFilter(Builder $query, $filter): void
    {
        if ($this->isNestedColumn($filter['column'])) {
            [$relation, $filter['column']] = explode('.', $filter['column']);
            $callable = Str::camel($relation);
            $filter['match'] = 'and';

            $query->orWhereHas(Str::camel($callable), function (Builder $q) use ($filter) {
                $this->{Str::camel($filter['operator'])}(
                    $filter,
                    $q
                );
            });
        } else {
            $filter['column'] = "{$this->table}.{$filter['column']}";
            $this->{Str::camel($filter['operator'])}(
                $filter,
                $query
            );
        }
    }

    protected function isNestedColumn(string $column): bool
    {
        return strpos($column, '.') !== false;
    }
}
