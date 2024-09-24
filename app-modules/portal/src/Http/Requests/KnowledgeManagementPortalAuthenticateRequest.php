<?php

namespace AidingApp\Portal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use AidingApp\Portal\Rules\PortalAuthenticateCodeValidation;

class KnowledgeManagementPortalAuthenticateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'integer', 'digits:6', new PortalAuthenticateCodeValidation()],
        ];
    }
}
