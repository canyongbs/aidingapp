<?php

namespace AidingApp\Portal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;

class StoreServiceRequestUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $contact = auth('contact')->user();

        $serviceRequestDetails = ServiceRequest::find($this->input('serviceRequestId'));

        return ! is_null($contact) && ! empty($serviceRequestDetails) && $serviceRequestDetails->respondent_type == 'contact' && $serviceRequestDetails->respondent_id == $contact->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'required'
        ];
    }
}
