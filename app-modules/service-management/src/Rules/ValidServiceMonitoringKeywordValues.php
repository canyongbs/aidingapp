<?php

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
