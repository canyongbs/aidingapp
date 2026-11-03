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
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Optional;

class SaveOrganization
{
    /**
     * Every writer of organization domains must hold this lock while checking and saving them. Advisory locks are
     * scoped to the database, so each tenant gets its own lock.
     */
    private const string DOMAINS_LOCK_KEY = 'organization-domains';

    /**
     * Creates the given organization, or updates it when an existing one is provided.
     * Only attributes present on the data object are written.
     *
     * @throws ValidationException When one of the domains is claimed by another organization.
     */
    public function __invoke(OrganizationDataObject $data, ?Organization $organization = null): Organization
    {
        $attributes = $data->toArray();

        if ($data->domains instanceof Optional) {
            unset($attributes['domains']);
        } else {
            $attributes['domains'] = filled($data->domains)
                ? array_map(fn (string $domain): array => ['domain' => $domain], array_values($data->domains))
                : null;
        }

        if ($organization === null) {
            $attributes = [
                'industry_id' => OrganizationIndustry::query()->where('is_default', true)->value('id'),
                'type_id' => OrganizationType::query()->where('is_default', true)->value('id'),
                ...$attributes,
            ];

            $organization = new Organization();
        }

        if (blank($attributes['domains'] ?? null)) {
            $organization->fill($attributes)->save();

            return $organization;
        }

        // A transaction alone does not stop two requests from both passing the domain check before either saves,
        // so the check is repeated under a lock that is held until the organization has been committed.
        DB::transaction(function () use ($organization, $attributes): void {
            DB::selectOne('SELECT pg_advisory_xact_lock(hashtext(?))', [self::DOMAINS_LOCK_KEY]);

            $this->ensureDomainsAreUnclaimed($organization, $attributes['domains']);

            $organization->fill($attributes)->save();
        });

        return $organization;
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
