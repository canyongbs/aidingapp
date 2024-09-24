<?php

namespace AidingApp\Portal\Rules;

use Closure;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class PortalAuthenticateCodeValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $request = request();

        $authentication = $request->route('authentication');

        if (! $authentication) {
            $fail('Could not find Authentication.');
        }

        if (! Hash::check($value, $authentication->code)) {
            $fail('The provided code is invalid.');
        }
    }
}
