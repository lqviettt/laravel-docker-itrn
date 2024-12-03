<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingFeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_type_id' => 'required|in:2,5',
            'province' => 'required|string',
            'district' => 'required|string',
            'ward' => 'nullable|string',
            'address' => 'nullable|string',
            'weight' => 'required|integer',
            'value' => 'required|integer',
        ];
    }
}
