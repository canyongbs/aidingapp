<?php

namespace AidingApp\ServiceManagement\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

class ManagedServiceRequestType implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (auth()->user()->hasRole('authorization.super_admin')) {
            return;
        }

        $team = auth()->user()->teams()->first();

        $isManager = ServiceRequestType::where('id', $value)
            ->whereHas('managers', function ($query) use ($team) {
                $query->where('teams.id', $team?->getKey());
            })
            ->exists();

        if (! $isManager) {
            $fail('You are not authorized to select this service request type.');
        }
    }
}
