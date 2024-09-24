<?php

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
