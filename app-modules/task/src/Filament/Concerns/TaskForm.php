<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Task\Filament\Concerns;

use AidingApp\Contact\Models\Contact;
use App\Models\Authenticatable;
use App\Models\Scopes\HasLicense;
use App\Models\User;
use Closure;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

trait TaskForm
{
    protected function scopeAssignmentRelationshipBasedOnConcern(): Closure
    {
        return function (Get $get, Builder $query) {
            $concernType = $get('concern_type');

            if (filled($concernType = (Relation::getMorphedModel($concernType) ?? $concernType))) {
                return $query->tap(new HasLicense($concernType::getLicenseType()));
            }

            /** @var Authenticatable $user */
            $user = auth()->user();

            $canAccessContacts = $user->hasLicense(Contact::getLicenseType());

            if ($canAccessContacts) {
                return $query;
            }

            return match (true) {
                $canAccessContacts => $query->tap(new HasLicense(Contact::getLicenseType())),
                default => $query,
            };
        };
    }

    protected function updateAssignmentAfterConcernSelected(): Closure
    {
        return function (Get $get, Set $set) {
            $concernId = $get('concern_id');

            if (blank($concernId)) {
                return;
            }

            $assignedTo = $get('assigned_to');

            if (blank($assignedTo)) {
                return;
            }

            /** @var Authenticatable $user */
            $user = auth()->user();

            $canAccessContacts = $user->hasLicense(Contact::getLicenseType());

            if ($canAccessContacts) {
                return;
            }

            $concernType = match (true) {
                $canAccessContacts => Contact::class,
            };

            $assignedTo = User::find($assignedTo);

            if ($assignedTo->hasLicense($concernType::getLicenseType())) {
                return;
            }

            $set('assigned_to', null);
        };
    }
}
