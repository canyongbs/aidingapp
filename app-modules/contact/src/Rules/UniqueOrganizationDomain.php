<?php

namespace AidingApp\Contact\Rules;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use AidingApp\Contact\Models\Organization;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueOrganizationDomain implements ValidationRule
{
    protected $ignoreId;

    /**
     * Create a new rule instance.
     *
     * @param  int|null  $ignoreId
     */
    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            Organization::whereJsonContains('domains', [['domain' => $value]])
                ->when(! empty($this->ignoreId), fn (Builder $query) => $query->where('id', '!=', $this->ignoreId))
                ->exists()
        ) {
            $fail($this->message());
        }
    }

    public function message()
    {
        return 'This domain is already in use and may not be used a second time.';
    }
}
