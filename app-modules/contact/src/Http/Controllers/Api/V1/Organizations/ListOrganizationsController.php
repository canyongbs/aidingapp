<?php

namespace AidingApp\Contact\Http\Controllers\Api\V1\Organizations;

use AidingApp\Contact\Http\Resources\Api\V1\OrganizationResource;
use AidingApp\Contact\Models\Organization;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ListOrganizationsController
{
    /**
     * @response AnonymousResourceCollection<LengthAwarePaginator<OrganizationResource>>
     */
    #[Group('Organizations')]
    #[QueryParameter('filter[name]', description: 'Filter the results where the name contains the provided value.', type: 'string')]
    #[QueryParameter('filter[email]', description: 'Filter the results where the email contains the provided value.', type: 'string')]
    #[QueryParameter('filter[industry]', description: 'Filter the results where the industry ID matches the provided value.', type: 'string')]
    #[QueryParameter('filter[type]', description: 'Filter the results where the type ID matches the provided value.', type: 'string')]
    #[QueryParameter('filter[is_contact_generation_enabled]', description: 'Filter the results by whether contact generation is enabled.', type: 'bool')]
    #[QueryParameter('page[number]', description: 'Control which page of organizations is returned in the response.', type: 'int', default: 1)]
    #[QueryParameter('page[size]', description: 'Control how many organizations are returned in the response.', type: 'int', default: 30)]
    #[QueryParameter('sort', description: 'Control the order of organizations that are returned in the response. Ascending order is used by default, prepend the sort with `-` to sort descending.', type: 'string', default: 'name', examples: [
        'name' => new Example('name'),
        'created_at' => new Example('created_at'),
        'updated_at' => new Example('updated_at'),
    ])]
    public function __invoke(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Organization::class);

        return QueryBuilder::for(
            Organization::query()->with(['industry', 'type'])
        )
            ->allowedFilters(
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
                AllowedFilter::belongsTo('industry', 'industry'),
                AllowedFilter::belongsTo('type', 'type'),
                AllowedFilter::exact('is_contact_generation_enabled'),
            )
            ->allowedSorts(
                AllowedSort::field('name'),
                AllowedSort::field('created_at'),
                AllowedSort::field('updated_at'),
            )
            ->defaultSort(AllowedSort::field('name'))
            ->jsonPaginate()
            ->toResourceCollection(OrganizationResource::class);
    }
}
