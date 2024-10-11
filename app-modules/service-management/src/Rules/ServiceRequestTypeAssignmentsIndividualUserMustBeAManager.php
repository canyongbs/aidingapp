<?php

namespace AidingApp\ServiceManagement\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

class ServiceRequestTypeAssignmentsIndividualUserMustBeAManager implements ValidationRule
{
    public function __construct(
        protected ServiceRequestType $serviceRequestType
    ) {}

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->serviceRequestType->managers()->whereRelation('users', 'users.id', $value)->doesntExist()) {
            $fail('The selected user must be in a team designated as managers of this Service Request Type.');
        }
    }
}
