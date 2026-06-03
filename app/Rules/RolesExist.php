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

namespace App\Rules;

use AidingApp\Authorization\Models\Role;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Fails when any of the provided role names does not match an existing web-guard role.
 *
 * The value is expected to be an array of role names (the import column splits on "|"). Matching is
 * case-insensitive because the role name column is citext. The import must never create roles, so an
 * unknown name is rejected rather than silently created.
 */
class RolesExist implements ValidationRule
{
    /**
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $names = collect(is_array($value) ? $value : [$value])
            ->map(fn (mixed $name): string => trim((string) $name))
            ->filter()
            ->unique()
            ->values();

        if ($names->isEmpty()) {
            return;
        }

        $existing = Role::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $names->all())
            ->pluck('name')
            ->map(fn (string $name): string => mb_strtolower($name))
            ->all();

        $missing = $names
            ->reject(fn (string $name): bool => in_array(mb_strtolower($name), $existing, true))
            ->values();

        if ($missing->isNotEmpty()) {
            $fail('The following role(s) do not exist: ' . $missing->implode(', ') . '.');
        }
    }
}
