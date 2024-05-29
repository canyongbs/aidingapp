<?php

namespace AidingApp\Portal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class GetServiceRequestUploadUrlRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array>
     */
    public function rules(): array
    {
        return [
            'filename' => ['required', 'string'],
        ];
    }
}
