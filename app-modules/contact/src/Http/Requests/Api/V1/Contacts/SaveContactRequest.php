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
use AidingApp\Contact\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class SaveContactRequest extends FormRequest
{
    /**
     * Request attributes whose names differ from the underlying contact columns.
     *
     * @var array<string, string>
     */
    protected const COLUMN_NAMES = [
        'preferred_name' => 'preferred',
        'postal_code' => 'postal',
    ];

    /**
     * @return array<string, mixed>
     */
    public function contactAttributes(): array
    {
        $attributes = [];

        foreach ($this->validated() as $key => $value) {
            $attributes[static::COLUMN_NAMES[$key] ?? $key] = $value;
        }

        return $attributes;
    }

    /**
     * @return array<string, array<mixed>>
     */
    protected function contactRules(?Contact $contact = null): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'preferred_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique(Contact::class, 'email')->ignore($contact)->withoutTrashed(),
            ],
            'mobile' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'organization_id' => ['nullable', 'uuid:4', Rule::exists(Organization::class, 'id')->whereNull('deleted_at')],
            'job_title' => ['nullable', 'string', 'max:255'],
            'employee_id' => ['nullable', 'string', 'max:255'],
            'work_number' => ['nullable', 'string', 'max:255'],
            'work_extension' => ['nullable', 'string', 'max:255'],
            'student_id' => ['nullable', 'string', 'max:255'],
            'school' => ['nullable', 'string', 'max:255'],
            'academic_department' => ['nullable', 'string', 'max:255'],
            'program' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
        ];
    }
}
