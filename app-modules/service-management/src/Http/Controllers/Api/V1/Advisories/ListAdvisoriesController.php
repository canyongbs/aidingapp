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

namespace AidingApp\ServiceManagement\Http\Controllers\Api\V1\Advisories;

use AidingApp\ServiceManagement\Http\Resources\Api\V1\AdvisoryResource;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use App\Features\AdvisoryVisibilityFeature;
use Closure;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ListAdvisoriesController
{
    /**
     * List advisories.
     *
     * @response AnonymousResourceCollection<LengthAwarePaginator<AdvisoryResource>>
     */
    #[Group('Advisories')]
    #[QueryParameter('filter[title]', description: 'Filter the results where the title contains the provided value.', type: 'string')]
    #[QueryParameter('filter[status]', description: 'Filter the results where the status name exactly matches the provided value, ignoring case. Separate multiple names with `|`.', type: 'string', examples: [
        'single' => new Example('Investigating'),
        'multiple' => new Example('Investigating|Monitoring'),
    ])]
    #[QueryParameter('filter[classification]', description: 'Filter the results by the classification of their status: `open` or `resolved`.', type: 'string', examples: [
        'open' => new Example('open'),
    ])]
    #[QueryParameter('filter[severity]', description: 'Filter the results where the severity name exactly matches the provided value, ignoring case. Separate multiple names with `|`.', type: 'string', examples: [
        'single' => new Example('Critical'),
        'multiple' => new Example('Critical|Major'),
    ])]
    #[QueryParameter('filter[department]', description: 'Filter the results where the department name exactly matches the provided value, ignoring case. Separate multiple names with `|`.', type: 'string', examples: [
        'single' => new Example('IT'),
        'multiple' => new Example('IT|Facilities'),
    ])]
    #[QueryParameter('page[number]', description: 'Control which page of advisories is returned in the response.', type: 'int', default: 1)]
    #[QueryParameter('page[size]', description: 'Control how many advisories are returned in the response.', type: 'int', default: 30)]
    #[QueryParameter('sort', description: 'Control the order of advisories that are returned in the response. `severity` orders them from the highest severity to the lowest, as in the admin panel. Ascending order is used by default, prepend the sort with `-` to sort descending.', type: 'string', default: 'title', examples: [
        'title' => new Example('title'),
        'severity' => new Example('severity'),
        'created_at' => new Example('created_at'),
        'updated_at' => new Example('updated_at'),
    ])]
    public function __invoke(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Advisory::class);

        return QueryBuilder::for(
            Advisory::query()->with([
                'severity',
                'status',
                'assignedDepartment',
                ...(AdvisoryVisibilityFeature::active() ? ['restrictedToContactTypes'] : []),
            ])
        )
            ->allowedFilters(
                AllowedFilter::partial('title'),
                // Names may contain commas, so multiple names are separated with a pipe instead.
                AllowedFilter::callback('status', $this->relatedNameFilter('status'))->delimiter('|'),
                AllowedFilter::callback('classification', fn (Builder $query, mixed $value) => $query->whereHas(
                    'status',
                    fn (Builder $query) => $query->whereIn('classification', Arr::wrap($value)),
                )),
                AllowedFilter::callback('severity', $this->relatedNameFilter('severity'))->delimiter('|'),
                AllowedFilter::callback('department', $this->relatedNameFilter('assignedDepartment'))->delimiter('|'),
            )
            ->allowedSorts(
                AllowedSort::field('title'),
                AllowedSort::callback('severity', function (Builder $query, bool $descending): void {
                    $direction = $descending ? 'desc' : 'asc';

                    $query
                        ->orderBy($this->severityColumn('sort'), $direction)
                        ->orderBy($this->severityColumn('name'), $direction);
                }),
                AllowedSort::field('created_at'),
                AllowedSort::field('updated_at'),
            )
            ->defaultSort(AllowedSort::field('title'))
            ->jsonPaginate()
            ->toResourceCollection(AdvisoryResource::class);
    }

    /**
     * Status, severity, and department names are `citext`, so they compare case-insensitively as they are.
     *
     * @return Closure(Builder<Advisory>, mixed): Builder<Advisory>
     */
    private function relatedNameFilter(string $relationship): Closure
    {
        return fn (Builder $query, mixed $value): Builder => $query->whereHas(
            $relationship,
            fn (Builder $query) => $query->whereIn('name', Arr::flatten(Arr::wrap($value))),
        );
    }

    /**
     * @return Builder<AdvisorySeverity>
     */
    private function severityColumn(string $column): Builder
    {
        return AdvisorySeverity::query()
            ->select("advisory_severities.{$column}")
            ->whereColumn('advisory_severities.id', 'advisories.severity_id');
    }
}
