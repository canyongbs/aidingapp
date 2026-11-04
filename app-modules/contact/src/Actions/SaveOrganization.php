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

namespace AidingApp\Contact\Actions;

use AidingApp\Contact\DataTransferObjects\OrganizationDataObject;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Models\OrganizationType;
use AidingApp\Contact\Rules\UniqueOrganizationDomain;
use AidingApp\Contact\Support\OrganizationDomainsLock;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Optional;

class SaveOrganization
{
    public function __construct(protected OrganizationDomainsLock $domainsLock) {}

    /**
     * Creates the given organization, or updates it when an existing one is provided.
     * Only attributes present on the data object are written.
     *
     * @throws ValidationException When one of the domains is claimed by another organization.
     */
    public function __invoke(OrganizationDataObject $data, ?Organization $organization = null): Organization
    {
        $attributes = Arr::except($data->toArray(), ['industry', 'type']);

        if (! $data->industry instanceof Optional) {
            $attributes['industry_id'] = $this->resolveId(OrganizationIndustry::class, 'industry', $data->industry);
        } elseif ($organization === null) {
            $attributes['industry_id'] = OrganizationIndustry::query()->where('is_default', true)->value('id');
        }

        if (! $data->type instanceof Optional) {
            $attributes['type_id'] = $this->resolveId(OrganizationType::class, 'type', $data->type);
        } elseif ($organization === null) {
            $attributes['type_id'] = OrganizationType::query()->where('is_default', true)->value('id');
        }

        if (! $data->domains instanceof Optional) {
            $attributes['domains'] = filled($data->domains)
                ? array_map(fn (string $domain): array => ['domain' => $domain], array_values($data->domains))
                : null;
        }

        $organization ??= new Organization();
        $organization->fill($attributes);

        if (blank($attributes['domains'] ?? null)) {
            $organization->save();
        } else {
            // The request was validated before the lock was taken, so the domains are checked again while holding it.
            DB::transaction(function () use ($organization, $attributes): void {
                $this->domainsLock->acquire();

                $this->ensureDomainsAreUnclaimed($organization, $attributes['domains']);

                $organization->save();
            });
        }

        return $organization;
    }

    /**
     * Industries and types are referenced by name, which is unique among those that have not been deleted.
     *
     * @param class-string<OrganizationIndustry|OrganizationType> $model
     *
     * @throws ValidationException
     */
    private function resolveId(string $model, string $field, ?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        $id = $model::query()->where('name', $name)->value('id');

        if (! is_string($id)) {
            throw ValidationException::withMessages([$field => "The selected {$field} is invalid."]);
        }

        return $id;
    }

    /**
     * @param array<int, array{domain: string}> $domains
     *
     * @throws ValidationException
     */
    private function ensureDomainsAreUnclaimed(Organization $organization, array $domains): void
    {
        $rule = new UniqueOrganizationDomain($organization->exists ? $organization->getKey() : null);

        foreach ($domains as $index => $domain) {
            $rule->validate("domains.{$index}", $domain['domain'], function (string $message) use ($index): never {
                throw ValidationException::withMessages(["domains.{$index}" => $message]);
            });
        }
    }
}
