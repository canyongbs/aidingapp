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

namespace AidingApp\ServiceManagement\Rules;

use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitorings\Schemas\Components\SuccessfulStatusCodesSelect;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidHttpStatusCodes implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Normalize a scalar into a single-element array rather than relying on (array) casting,
        // matching the pattern used elsewhere for array-typed rules (see RolesExist).
        $codes = is_array($value) ? $value : [$value];

        $invalid = collect($codes)->reject(fn (mixed $code): bool => $this->isValidCode($code));

        if ($invalid->isNotEmpty()) {
            $fail('Each status code must be a valid HTTP status code.');
        }
    }

    /**
     * Requires the value to already be an int, or a string whose canonical int form doesn't
     * lose information (e.g. rejects '200junk' and '200.9', which (int) casting would silently
     * accept as 200).
     */
    protected function isValidCode(mixed $code): bool
    {
        if (is_int($code)) {
            return array_key_exists($code, SuccessfulStatusCodesSelect::options());
        }

        if (is_string($code) && ctype_digit($code)) {
            return array_key_exists((int) $code, SuccessfulStatusCodesSelect::options());
        }

        return false;
    }
}
