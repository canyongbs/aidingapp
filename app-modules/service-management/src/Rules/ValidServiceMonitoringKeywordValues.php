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

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidServiceMonitoringKeywordValues implements ValidationRule
{
    public function __construct(protected mixed $shouldContain, protected mixed $shouldNotContain) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && ! $this->hasValidQuotePlacement($value)) {
            $fail('Double quotes must be paired.');

            return;
        }

        if (is_string($value) && blank(array_filter(array_map('trim', str_getcsv($value)), filled(...)))) {
            $fail('Keyword values cannot be empty.');

            return;
        }

        if (array_intersect($this->parse($this->shouldContain), $this->parse($this->shouldNotContain)) !== []) {
            $fail('A keyword value cannot appear in both lists.');

            return;
        }

        if (! str_ends_with($attribute, 'should_not_contain')) {
            return;
        }

        foreach ($this->parse($this->shouldNotContain) as $prohibitedValue) {
            foreach ($this->parse($this->shouldContain) as $requiredValue) {
                if (str_contains(mb_strtolower($requiredValue), mb_strtolower($prohibitedValue))) {
                    $fail('A prohibited keyword cannot be a substring of a required keyword.');

                    return;
                }
            }
        }
    }

    protected function hasValidQuotePlacement(string $value): bool
    {
        return preg_match('/^\s*(?:"[^"]+"|[^",]*)\s*(?:,\s*(?:"[^"]+"|[^",]*)\s*)*$/', $value) === 1;
    }

    /**
     * @return list<string>
     */
    protected function parse(mixed $value): array
    {
        if (! is_string($value)) {
            return [];
        }

        return array_values(array_unique(array_filter(
            array_map('trim', str_getcsv($value)),
            filled(...),
        )));
    }
}
