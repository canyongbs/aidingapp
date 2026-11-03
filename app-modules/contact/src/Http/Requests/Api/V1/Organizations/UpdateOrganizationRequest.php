<?php

namespace AidingApp\Contact\Http\Requests\Api\V1\Organizations;

use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Models\OrganizationType;
use AidingApp\Contact\Rules\UniqueOrganizationDomain;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Organization::class)
            && Gate::allows('update', $this->route('organization'));
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $organization = $this->route('organization');

        assert($organization instanceof Organization);

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique(Organization::class, 'name')->ignore($organization->getKey())->withoutTrashed()],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'url', 'max:255'],
            'industry_id' => ['nullable', 'uuid:4', Rule::exists(OrganizationIndustry::class, 'id')],
            'type_id' => ['nullable', 'uuid:4', Rule::exists(OrganizationType::class, 'id')],
            'description' => ['nullable', 'string'],
            'number_of_employees' => ['nullable', 'integer', 'min:0', 'max:2147483647'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postalcode' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'string', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'string', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'string', 'url', 'max:255'],
            'domains' => ['nullable', 'array'],
            'domains.*' => ['required', 'string', 'max:255', 'regex:/^(?!-)([a-zA-Z0-9-]{1,63}\.)+[a-zA-Z]{2,63}$/', 'distinct', new UniqueOrganizationDomain($organization->getKey())],
            'is_contact_generation_enabled' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $domains = $this->input('domains');

        if (is_array($domains)) {
            $this->merge([
                'domains' => array_map(
                    fn (mixed $domain): mixed => is_string($domain) ? mb_strtolower(trim($domain)) : $domain,
                    $domains,
                ),
            ]);
        }
    }
}
