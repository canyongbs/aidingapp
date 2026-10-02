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

namespace AidingApp\Contact\Observers;

use AidingApp\Contact\Jobs\MatchUnaffiliatedContactsJob;
use AidingApp\Contact\Models\Organization;

class OrganizationObserver
{
    public function created(Organization $organization): void
    {
        if (blank($organization->domains)) {
            return;
        }

        $this->dispatchMatching($organization);
    }

    public function updated(Organization $organization): void
    {
        if (! $organization->wasChanged('domains') || blank($organization->domains)) {
            return;
        }

        $this->dispatchMatching($organization);
    }

    private function dispatchMatching(Organization $organization): void
    {
        dispatch((new MatchUnaffiliatedContactsJob((string) $organization->getKey()))->afterCommit());
    }
}
