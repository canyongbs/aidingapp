<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^(https?:\/\/)?((([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,})|(((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))|(\[[A-Fa-f0-9:]+\])|([0-9a-fA-F]{1,4}(:[0-9a-fA-F]{1,4}){7}))(:\d+)?(\/.*)?$/', $value)) {
            $fail('Please enter a valid URL.');
        }
    }
}
