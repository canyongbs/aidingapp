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

namespace AidingApp\Contact\Http\Requests\Api\V1\Contacts;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Rules\ContactEmailNotArchived;
use App\Features\ContactArchivingFeature;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends FormRequest
{
    /**
     * Authorization runs before validation so unauthorized callers cannot probe
     * the data through validation messages.
     */
    public function authorize(): bool
    {
        $contact = $this->route('contact');

        assert($contact instanceof Contact);

        Gate::authorize('viewAny', Contact::class);
        Gate::authorize('update', $contact);

        return true;
    }

    /**
     * Every attribute is optional, and only the ones that are sent are validated and updated. The attributes
     * a contact must always have (names and type) cannot be sent as `null`, because they are not `nullable`.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        // The route parameter is unavailable when Scramble generates the API documentation.
        $contact = $this->route('contact');
        $contact = $contact instanceof Contact ? $contact : null;

        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'preferred_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
                // Stops at the first failure, so an archived match shows its own message instead of the generic unique one.
                Rule::when(fn (): bool => ContactArchivingFeature::active(), ['bail', new ContactEmailNotArchived(ignore: $contact)]),
                Rule::unique(Contact::class, 'email')->ignore($contact)->withoutTrashed(),
            ],
            'mobile' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:255'],
            'organization' => ['sometimes', 'nullable', 'string', 'max:255'],
            'job_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'employee_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'work_number' => ['sometimes', 'nullable', 'string', 'max:255'],
            'work_extension' => ['sometimes', 'nullable', 'string', 'max:255'],
            'student_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'school' => ['sometimes', 'nullable', 'string', 'max:255'],
            'academic_department' => ['sometimes', 'nullable', 'string', 'max:255'],
            'program' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address_2' => ['sometimes', 'nullable', 'string', 'max:255'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'state' => ['sometimes', 'nullable', 'string', 'max:255'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:255'],
            'country' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
