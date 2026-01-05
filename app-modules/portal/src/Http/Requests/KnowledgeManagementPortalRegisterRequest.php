<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Portal\Http\Requests;

use AidingApp\Contact\Models\Organization;
use AidingApp\Portal\Rules\PortalAuthenticateCodeValidation;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KnowledgeManagementPortalRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        $email = $this->input('email');

        preg_match('/@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/', $email, $matches);

        $domain = $matches[1];

        return Organization::query()
            ->whereRaw(
                "EXISTS (
                    SELECT 1
                    FROM jsonb_array_elements(domains) AS elem
                    WHERE LOWER(elem->>'domain') = ?
                )",
                [strtolower($domain)]
            )
            ->where('is_contact_generation_enabled', true)
            ->exists();
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->where(fn (Builder $query) => $query->whereNotNull('deleted_at'))],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'preferred' => ['nullable', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'sms_opt_out' => ['required', 'boolean'],
            'code' => ['required', 'integer', 'digits:6', new PortalAuthenticateCodeValidation()],
        ];
    }
}
