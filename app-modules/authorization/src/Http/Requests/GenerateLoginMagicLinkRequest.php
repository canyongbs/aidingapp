<?php

namespace AidingApp\Authorization\Http\Requests;

use App\Models\Authenticatable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateLoginMagicLinkRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in([Authenticatable::SUPER_ADMIN_ROLE])],
        ];
    }
}
