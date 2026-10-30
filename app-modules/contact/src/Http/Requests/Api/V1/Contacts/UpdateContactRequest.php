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
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends SaveContactRequest
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
     * Only the attributes present in the request are validated and updated.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        $contact = $this->route('contact');

        $rules = [
            ...$this->contactRules($contact instanceof Contact ? $contact : null),
            'type_id' => ['required', 'uuid:4', Rule::exists(ContactType::class, 'id')->whereNull('deleted_at')],
        ];

        return array_map(fn (array $fieldRules): array => ['sometimes', ...$fieldRules], $rules);
    }

    /**
     * @return array<string, mixed>
     */
    public function contactAttributes(): array
    {
        $attributes = parent::contactAttributes();

        if (array_key_exists('first_name', $attributes) || array_key_exists('last_name', $attributes)) {
            $contact = $this->route('contact');

            assert($contact instanceof Contact);

            $attributes['full_name'] = ($attributes['first_name'] ?? $contact->first_name) . ' ' . ($attributes['last_name'] ?? $contact->last_name);
        }

        return $attributes;
    }
}
