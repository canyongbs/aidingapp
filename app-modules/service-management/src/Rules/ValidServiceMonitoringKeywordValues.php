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
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidServiceMonitoringKeywordValues implements DataAwareRule, ValidationRule
{
    /**
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && ! $this->hasValidQuotePlacement($value)) {
            $fail('Double quotes must be paired.');

            return;
        }

        if (is_string($value) && blank(self::parseValues($value))) {
            $fail('Keyword values cannot be empty.');

            return;
        }

        $shouldContain = array_map(
            fn (string $value): string => mb_strtolower($value),
            self::parseValues($this->fieldValue('should_contain')),
        );
        $shouldNotContain = array_map(
            fn (string $value): string => mb_strtolower($value),
            self::parseValues($this->fieldValue('should_not_contain')),
        );

        if (array_intersect($shouldContain, $shouldNotContain) !== []) {
            $fail('A keyword value cannot appear in both lists.');

            return;
        }

        if (! str_ends_with($attribute, 'should_not_contain')) {
            return;
        }

        foreach (self::parseValues($this->fieldValue('should_not_contain')) as $prohibitedValue) {
            foreach (self::parseValues($this->fieldValue('should_contain')) as $requiredValue) {
                if (str_contains(mb_strtolower($requiredValue), mb_strtolower($prohibitedValue))) {
                    $fail('A prohibited keyword cannot be a substring of a required keyword.');

                    return;
                }
            }
        }
    }

    protected function hasValidQuotePlacement(string $value): bool
    {
        return preg_match('/^\s*(?:".*?"|[^",]*)\s*(?:,\s*(?:".*?"|[^",]*)\s*)*$/', $value) === 1;
    }

    protected function fieldValue(string $field): mixed
    {
        return data_get($this->data, "data.data.{$field}", data_get($this->data, "data.{$field}", data_get($this->data, $field)));
    }

    /**
     * @return list<string>
     */
    public static function parseValues(mixed $value): array
    {
        if (! is_string($value)) {
            return [];
        }

        preg_match_all('/(?:^|,)\s*(?:"(.*?)"|([^",]*))\s*(?=,|$)/', $value, $matches);

        $values = array_map(
            fn (string $quotedValue, string $unquotedValue): string => trim($quotedValue !== '' ? $quotedValue : $unquotedValue),
            $matches[1],
            $matches[2],
        );

        return array_values(array_unique(array_filter($values, filled(...))));
    }
}
