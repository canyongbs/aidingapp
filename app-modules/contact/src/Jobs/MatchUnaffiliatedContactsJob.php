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
      same in return. Canyon GBS® and Aiding App® are registered trademarks, and we are
      committed to enforcing and protecting our trademarks vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.

</COPYRIGHT>
*/

namespace AidingApp\Contact\Jobs;

use AidingApp\Contact\Actions\MatchContactToOrganization;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class MatchUnaffiliatedContactsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ?string $organizationId = null,
    ) {}

    public function handle(MatchContactToOrganization $matchContactToOrganization): void
    {
        $organization = $this->organizationId === null
            ? null
            : Organization::query()->findOrFail($this->organizationId);

        Contact::query()
            ->select(['id', 'email', 'organization_id'])
            ->whereNull('organization_id')
            ->whereNotNull('email')
            ->chunkById(100, function (Collection $contacts) use ($matchContactToOrganization, $organization): void {
                foreach ($contacts as $contact) {
                    $matchContactToOrganization($contact, $organization);
                }
            });
    }

    public function failed(?Throwable $exception): void
    {
        if ($exception !== null) {
            report($exception);
        }
    }
}
