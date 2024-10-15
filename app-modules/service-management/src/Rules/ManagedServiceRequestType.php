<?php

namespace AidingApp\ServiceManagement\Rules;

use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class ManagedServiceRequestType implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
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
                        ->orWhereHas('auditors', function ($query) use ($team) {
                            $query->where('teams.id', $team?->getKey());
                        })
                        ->exists();
        
        
        if (! $isManager) {
            $fail('You are not authorized to select this service request type.');
        }
    }
}
