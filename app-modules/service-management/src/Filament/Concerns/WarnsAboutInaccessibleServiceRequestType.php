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

namespace AidingApp\ServiceManagement\Filament\Concerns;

use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;

trait WarnsAboutInaccessibleServiceRequestType
{
    /** @var array<string, string> */
    protected array $inaccessibleTypeWarnings = [];

    public static function inaccessibleTypeWarningMessage(string $typeName): string
    {
        return "This service request will be created in the catalog under {$typeName}. Since you're not a manager or auditor for this type, you won't be able to view the request's details after it's created.";
    }

    /**
     * The warning for the selected type, or null when the user can see requests of that type.
     * Memoized per type so the callout's visibility and content checks share one set of lookups.
     */
    protected function getInaccessibleTypeWarning(?string $typeId): ?string
    {
        if (blank($typeId)) {
            return null;
        }

        return $this->inaccessibleTypeWarnings[$typeId] ??= $this->resolveInaccessibleTypeWarning($typeId);
    }

    protected function resolveInaccessibleTypeWarning(string $typeId): string
    {
        $user = auth()->user();

        if (! $user instanceof User || $user->isSuperAdmin()) {
            return '';
        }

        $type = ServiceRequestType::find($typeId);

        if (is_null($type) || $type->isManagedBy($user) || $type->isAuditedBy($user)) {
            return '';
        }

        return self::inaccessibleTypeWarningMessage($type->name);
    }
}
