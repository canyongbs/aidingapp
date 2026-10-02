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

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Support\OrganizationEmailDomainLookup;
use Illuminate\Support\Facades\DB;

class MatchContactToOrganization
{
    public function __construct(
        private OrganizationEmailDomainLookup $domainLookup,
    ) {}

    public function __invoke(Contact $contact, ?Organization $organization = null): void
    {
        if ($contact->organization_id !== null || blank($contact->email)) {
            return;
        }

        DB::transaction(function () use ($contact, $organization): void {
            $contact = Contact::query()
                ->whereKey($contact->getKey())
                ->whereNull('organization_id')
                ->lockForUpdate()
                ->first();

            if ($contact === null || blank($contact->email)) {
                return;
            }

            $organization = $this->domainLookup->find($contact->email, $organization);

            if ($organization === null) {
                return;
            }

            $contact->organization()->associate($organization);
            $contact->save();
        });
    }
}
