<?php

namespace AidingApp\Portal\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use AidingApp\Portal\Rules\PortalAuthenticateCodeValidation;

class KnowledgeManagementPortalRegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->where(fn (Builder $query) => $query->whereNotNull('deleted_at'))],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'preferred_name' => ['nullable', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'sms_opt_out' => ['required', 'boolean'],
            'code' => ['required', 'integer', 'digits:6', new PortalAuthenticateCodeValidation()],
        ];
    }
}
