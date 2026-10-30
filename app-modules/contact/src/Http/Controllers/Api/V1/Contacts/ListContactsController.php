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

namespace AidingApp\Contact\Http\Controllers\Api\V1\Contacts;

use AidingApp\Contact\Http\Resources\Api\V1\ContactResource;
use AidingApp\Contact\Models\Contact;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ListContactsController
{
    /**
     * @response AnonymousResourceCollection<LengthAwarePaginator<ContactResource>>
     */
    #[Group('Contacts')]
    #[QueryParameter('filter[name]', description: 'Filter the results where the full name partially matches the provided value.', type: 'string')]
    #[QueryParameter('filter[email]', description: 'Filter the results where the email address partially matches the provided value.', type: 'string')]
    #[QueryParameter('filter[type]', description: 'Filter the results where the contact type ID matches the provided value.', type: 'string')]
    #[QueryParameter('filter[organization]', description: 'Filter the results where the organization ID matches the provided value.', type: 'string')]
    #[QueryParameter('page[number]', description: 'Control which page of contacts is returned in the response.', type: 'int', default: 1)]
    #[QueryParameter('page[size]', description: 'Control how many contacts are returned in the response.', type: 'int', default: 30)]
    #[QueryParameter('sort', description: 'Control the order of contacts that are returned in the response. Ascending order is used by default, prepend the sort with `-` to sort descending.', type: 'string', default: 'name', examples: [
        'name' => new Example('name'),
        'email' => new Example('email'),
        'created_at' => new Example('created_at'),
    ])]
    public function __invoke(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Contact::class);

        return QueryBuilder::for(
            Contact::query()->with(['type', 'organization'])
        )
            ->allowedFilters(
                AllowedFilter::partial('name', 'full_name'),
                AllowedFilter::partial('email'),
                AllowedFilter::exact('type', 'type_id'),
                AllowedFilter::exact('organization', 'organization_id'),
            )
            ->allowedSorts(
                AllowedSort::field('name', 'full_name'),
                AllowedSort::field('email'),
                AllowedSort::field('created_at'),
            )
            ->defaultSort(AllowedSort::field('name', 'full_name'))
            ->jsonPaginate()
            ->toResourceCollection(ContactResource::class);
    }
}
