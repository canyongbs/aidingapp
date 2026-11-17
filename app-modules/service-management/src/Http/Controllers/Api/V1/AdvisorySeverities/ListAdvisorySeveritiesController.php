<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\ServiceManagement\Http\Controllers\Api\V1\AdvisorySeverities;

use AidingApp\ServiceManagement\Http\Resources\Api\V1\AdvisorySeverityResource;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ListAdvisorySeveritiesController
{
    /**
     * List advisory severities.
     *
     * @response AnonymousResourceCollection<LengthAwarePaginator<AdvisorySeverityResource>>
     */
    #[Group('Advisory Severities')]
    #[QueryParameter('filter[name]', description: 'Filter the results where the name contains the provided value.', type: 'string')]
    #[QueryParameter('filter[color]', description: 'Filter the results by color.', type: 'string', examples: [
        'red' => new Example('red'),
    ])]
    #[QueryParameter('page[number]', description: 'Control which page of advisory severities is returned in the response.', type: 'int', default: 1)]
    #[QueryParameter('page[size]', description: 'Control how many advisory severities are returned in the response.', type: 'int', default: 30)]
    #[QueryParameter('sort', description: 'Control the order of advisory severities that are returned in the response. `sort` orders them from the highest severity to the lowest, as in the admin panel and portal. Ascending order is used by default, prepend the sort with `-` to sort descending.', type: 'string', default: 'sort', examples: [
        'sort' => new Example('sort'),
        'name' => new Example('name'),
        'created_at' => new Example('created_at'),
        'updated_at' => new Example('updated_at'),
    ])]
    public function __invoke(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', AdvisorySeverity::class);

        $sort = AllowedSort::callback('sort', function (Builder $query, bool $descending): void {
            $direction = $descending ? 'desc' : 'asc';

            $query
                ->orderBy('sort', $direction)
                ->orderBy('name', $direction);
        });

        return QueryBuilder::for(AdvisorySeverity::query())
            ->allowedFilters(
                AllowedFilter::partial('name'),
                AllowedFilter::exact('color'),
            )
            ->allowedSorts(
                $sort,
                AllowedSort::field('name'),
                AllowedSort::field('created_at'),
                AllowedSort::field('updated_at'),
            )
            ->defaultSort($sort)
            ->jsonPaginate()
            ->toResourceCollection(AdvisorySeverityResource::class);
    }
}
