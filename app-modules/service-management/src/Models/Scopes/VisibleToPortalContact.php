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

namespace AidingApp\ServiceManagement\Models\Scopes;

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Enums\AdvisoryVisibility;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisoryVisibilityContactType;
use App\Features\AdvisoryVisibilityFeature;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Limits advisories to those the portal visitor may see: public advisories for everyone, and authenticated advisories
 * for a signed-in contact, narrowed to the contact's type when the advisory is restricted to specific contact types.
 */
class VisibleToPortalContact
{
    public function __construct(protected ?Contact $contact) {}

    /**
     * @param Builder<Advisory> $query
     */
    public function __invoke(Builder $query): void
    {
        if (! AdvisoryVisibilityFeature::active()) {
            // Until the migration runs, advisories were only ever available to signed-in contacts.
            if ($this->contact === null) {
                $query->whereRaw('false');
            }

            return;
        }

        $query->where(function (Builder $query): void {
            $query->where('advisories.visibility', AdvisoryVisibility::Public);

            if ($this->contact === null) {
                return;
            }

            $query->orWhere(function (Builder $query): void {
                $query
                    ->where('advisories.visibility', AdvisoryVisibility::Authenticated)
                    ->where(function (Builder $query): void {
                        $query
                            ->where('advisories.is_visibility_restricted', false)
                            ->orWhereIn('advisories.id', DB::table((new AdvisoryVisibilityContactType())->getTable())
                                ->select('advisory_id')
                                ->where('contact_type_id', $this->contact?->type_id));
                    });
            });
        });
    }
}
