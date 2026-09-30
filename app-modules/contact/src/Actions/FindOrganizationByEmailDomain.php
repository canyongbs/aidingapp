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

</COPYRIGHT>
*/

namespace AidingApp\Contact\Actions;

use AidingApp\Contact\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FindOrganizationByEmailDomain
{
    public function __invoke(
        string $email,
        ?Organization $organization = null,
        bool $requireContactGenerationEnabled = false,
    ): ?Organization {
        $emailDomain = $this->extractEmailDomain($email);

        if ($emailDomain === null) {
            return null;
        }

        return Organization::query()
            ->when($organization, fn (Builder $query) => $query->whereKey($organization->getKey()))
            ->when($requireContactGenerationEnabled, fn (Builder $query) => $query->where('is_contact_generation_enabled', true))
            ->whereRaw(
                "EXISTS (
                    SELECT 1
                    FROM jsonb_array_elements(domains) AS elem
                    WHERE LOWER(
                        regexp_replace(
                            regexp_replace(
                                regexp_replace(
                                    regexp_replace(trim(elem->>'domain'), '^https?://', '', 'i'),
                                    '/.*$',
                                    ''
                                ),
                                ':[0-9]+$',
                                ''
                            ),
                            '^www\\.',
                            '',
                            'i'
                        )
                    ) = ?
                )",
                [$emailDomain],
            )
            ->first();
    }

    private function extractEmailDomain(string $email): ?string
    {
        if (! Str::contains($email, '@')) {
            return null;
        }

        $emailDomain = Str::afterLast($email, '@');

        if (blank($emailDomain)) {
            return null;
        }

        return Str::lower($emailDomain);
    }
}
