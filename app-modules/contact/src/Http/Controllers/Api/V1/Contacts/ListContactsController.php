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
use Closure;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ListContactsController
{
    /**
     * List contacts. Archived contacts are not included.
     *
     * @response AnonymousResourceCollection<LengthAwarePaginator<ContactResource>>
     */
    #[Group('Contacts')]
    #[QueryParameter('filter[name]', description: 'Filter the results where the full name partially matches the provided value.', type: 'string')]
    #[QueryParameter('filter[email]', description: 'Filter the results where the email address partially matches the provided value.', type: 'string')]
    #[QueryParameter('filter[type]', description: 'Filter the results where the contact type name exactly matches the provided value, ignoring case. Separate multiple names with `|`.', type: 'string', examples: [
        'single' => new Example('Customer'),
        'multiple' => new Example('Customer|Prospect'),
    ])]
    #[QueryParameter('filter[organization]', description: 'Filter the results where the organization name exactly matches the provided value, ignoring case. Separate multiple names with `|`.', type: 'string', examples: [
        'single' => new Example('Acme University'),
        'multiple' => new Example('Acme University|Globex Institute'),
    ])]
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
            Contact::query()->withoutArchived()->with(['type', 'organization'])
        )
            ->allowedFilters(
                AllowedFilter::partial('name', 'full_name'),
                AllowedFilter::partial('email'),
                // Names may contain commas, so multiple names are separated with a pipe instead.
                AllowedFilter::callback('type', $this->relatedNameFilter('type'))->delimiter('|'),
                AllowedFilter::callback('organization', $this->relatedNameFilter('organization', isNameCaseInsensitive: true))->delimiter('|'),
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

    /**
     * Match the related record's name exactly, ignoring case. Organization names are `citext`, so they
     * compare case-insensitively as they are, whereas contact type names need `lower()`.
     *
     * @return Closure(Builder<Contact>, mixed): Builder<Contact>
     */
    private function relatedNameFilter(string $relationship, bool $isNameCaseInsensitive = false): Closure
    {
        return function (Builder $query, mixed $value) use ($relationship, $isNameCaseInsensitive): Builder {
            $names = Arr::flatten(Arr::wrap($value));

            if (! $isNameCaseInsensitive) {
                $names = array_map(fn (mixed $name): string => Str::lower((string) $name), $names);
            }

            return $query->whereHas(
                $relationship,
                fn (Builder $query) => $query->whereIn(
                    $isNameCaseInsensitive ? 'name' : new Expression('lower(name)'),
                    $names,
                ),
            );
        };
    }
}
