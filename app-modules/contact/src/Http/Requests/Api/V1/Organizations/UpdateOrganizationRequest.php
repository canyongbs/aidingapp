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
        // The route parameter is unavailable when Scramble generates the API documentation.
        $organization = $this->route('organization');
        $organizationId = $organization instanceof Organization ? $organization->getKey() : null;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique(Organization::class, 'name')->ignore($organizationId)->withoutTrashed()],
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
            'domains.*' => ['required', 'string', 'max:255', 'regex:/^(?!-)([a-zA-Z0-9-]{1,63}\.)+[a-zA-Z]{2,63}$/', 'distinct', new UniqueOrganizationDomain($organizationId)],
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
