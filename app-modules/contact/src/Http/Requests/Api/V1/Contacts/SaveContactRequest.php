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
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Support\ContactFullName;
use App\Features\ContactArchivingFeature;
use Closure;
use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Validator;

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

    protected ?ContactType $resolvedType = null;

    protected ?Organization $resolvedOrganization = null;

    /**
     * @return array<string, mixed>
     */
    public function contactAttributes(): array
    {
        $attributes = [];

        foreach ($this->validated() as $key => $value) {
            if (in_array($key, ['type', 'organization'], true)) {
                continue;
            }

            $attributes[static::COLUMN_NAMES[$key] ?? $key] = $value;
        }

        return $attributes;
    }

    public function getResolvedType(): ?ContactType
    {
        return $this->resolvedType;
    }

    public function getResolvedOrganization(): ?Organization
    {
        return $this->resolvedOrganization;
    }

    /**
     * @return array<Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                foreach (['type_id' => 'type', 'organization_id' => 'organization'] as $field => $replacement) {
                    if ($this->exists($field)) {
                        $validator->errors()->add($field, "The {$field} field is not supported. Provide the {$replacement} name in the {$replacement} field instead.");
                    }
                }
            },
            function (Validator $validator): void {
                $type = $this->input('type');

                if (! $validator->errors()->has('type') && is_string($type)) {
                    $types = ContactType::query()
                        ->where(new Expression('lower(name)'), '=', Str::lower($type))
                        ->limit(2)
                        ->get();

                    if ($types->isEmpty()) {
                        $validator->errors()->add('type', 'The specified contact type does not exist.');
                    } elseif ($types->count() > 1) {
                        $validator->errors()->add('type', 'The specified contact type name is ambiguous.');
                    } else {
                        $this->resolvedType = $types->first();
                    }
                }

                $organization = $this->input('organization');

                if (! $validator->errors()->has('organization') && is_string($organization)) {
                    $this->resolvedOrganization = Organization::query()
                        ->where(new Expression('lower(name)'), '=', Str::lower($organization))
                        ->first();

                    if ($this->resolvedOrganization === null) {
                        $validator->errors()->add('organization', 'The specified organization does not exist.');
                    }
                }
            },
            function (Validator $validator): void {
                if ($validator->errors()->has('first_name') || $validator->errors()->has('last_name')) {
                    return;
                }

                $contact = $this->route('contact');
                $firstName = $this->input('first_name', $contact instanceof Contact ? $contact->first_name : null);
                $lastName = $this->input('last_name', $contact instanceof Contact ? $contact->last_name : null);

                if (! is_string($firstName) || ! is_string($lastName) || ! ContactFullName::exceedsMaxLength($firstName, $lastName)) {
                    return;
                }

                foreach (['first_name', 'last_name'] as $field) {
                    if ($this->exists($field)) {
                        $validator->errors()->add($field, ContactFullName::tooLongMessage());
                    }
                }
            },
        ];
    }

    /**
     * @return array<string, array<mixed>>
     */
    protected function contactRules(?Contact $contact = null, bool $ignoreArchivedEmails = false): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'preferred_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique(Contact::class, 'email')
                    ->ignore($contact)
                    ->withoutTrashed()
                    ->when(
                        $ignoreArchivedEmails && ContactArchivingFeature::active(),
                        fn (Unique $rule): Unique => $rule->whereNull('archived_at'),
                    ),
            ],
            'mobile' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
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
