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

namespace AidingApp\ServiceManagement\Http\Requests\Api\V1\Advisories;

use AidingApp\Contact\Rules\ResolvableContactTypeName;
use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Enums\AdvisoryVisibility;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use App\Features\AdvisoryVisibilityFeature;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateAdvisoryRequest extends FormRequest
{
    /**
     * Authorization runs before validation so unauthorized callers cannot probe
     * the data through validation messages.
     */
    public function authorize(): bool
    {
        Gate::authorize('viewAny', Advisory::class);
        Gate::authorize('update', $this->route('advisory'));

        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string', 'max:65535'],
            /** The name of the advisory severity. */
            'severity' => ['sometimes', 'required', 'string', 'max:255', Rule::exists(AdvisorySeverity::class, 'name')->withoutTrashed()],
            /** The name of the advisory status. */
            'status' => ['sometimes', 'required', 'string', 'max:255', Rule::exists(AdvisoryStatus::class, 'name')->withoutTrashed()],
            /** The name of the department to assign the advisory to, or `null` to unassign it. */
            'department' => ['sometimes', 'nullable', 'string', 'max:255', Rule::exists(Department::class, 'name')],
            ...(AdvisoryVisibilityFeature::active() ? [
                'visibility' => ['sometimes', 'required', Rule::enum(AdvisoryVisibility::class)],
                /** Whether only contacts of the `restricted_to_contact_types` can see an `authenticated` advisory. */
                'is_visibility_restricted' => ['sometimes', 'required', 'boolean'],
                /** The names of the contact types that can see the advisory, replacing the current ones. Required when the advisory is `authenticated` and restricted, and it has no contact types yet. */
                'restricted_to_contact_types' => ['sometimes', 'nullable', 'array'],
                'restricted_to_contact_types.*' => ['required', 'string', 'max:255', 'distinct:ignore_case', new ResolvableContactTypeName()],
            ] : []),
        ];
    }
}
