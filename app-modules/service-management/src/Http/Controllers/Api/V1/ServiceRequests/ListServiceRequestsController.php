<?php

namespace AidingApp\ServiceManagement\Http\Controllers\Api\V1\ServiceRequests;

use AidingApp\ServiceManagement\Http\Resources\Api\V1\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ListServiceRequestsController
{
    #[Group('Service Requests')]
    #[QueryParameter('filter[status_id]', description: 'Filter the results where the status ID matches the provided value.', type: 'string')]
    #[QueryParameter('filter[assignee_id]', description: 'Filter the results where the assignee ID matches the provided value.', type: 'string')]
    #[QueryParameter('filter[respondent_id]', description: 'Filter the results where the respondent (contact) ID matches the provided value.', type: 'string')]
    #[QueryParameter('filter[title]', description: 'Filter the results where the title matches the provided value.', type: 'string')]
    #[QueryParameter('page[number]', description: 'Control which page of service requests is returned in the response.', type: 'int', default: 1)]
    #[QueryParameter('page[size]', description: 'Control how many service requests are returned in the response.', type: 'int', default: 30)]
    #[QueryParameter('sort', description: 'Control the order of service requests that are returned in the response. Ascending order is used by default, prepend the sort with `-` to sort descending.', type: 'string', default: 'title', examples: [
        'title' => new Example('title'),
        'service_request_number' => new Example('service_request_number'),
        'created_at' => new Example('created_at'),
    ])]
    public function __invoke(Request $request)
    {
        Gate::authorize('viewAny', ServiceRequest::class);

        return QueryBuilder::for(ServiceRequest::class)
            ->allowedFilters(
                AllowedFilter::exact('status_id'),
                AllowedFilter::exact('assignee_id', 'assignedTo.user_id'),
                AllowedFilter::exact('respondent_id', 'respondent.user_id'),
                AllowedFilter::partial('title'),
            )
            ->allowedIncludes(
                AllowedInclude::relationship('assignee_id', 'assignedTo.user'),
                AllowedInclude::relationship('status_id', 'status'),
                AllowedInclude::relationship('priority_id', 'priority'),
                AllowedInclude::relationship('respondent_id', 'respondent'),
            )
            ->allowedSorts(
                AllowedSort::field('title'),
                AllowedSort::field('service_request_number'),
                AllowedSort::field('created_at'),
            )
            ->defaultSort(AllowedSort::field('title'))
            ->jsonPaginate()
            ->toResourceCollection(ServiceRequestResource::class);
    }
}
