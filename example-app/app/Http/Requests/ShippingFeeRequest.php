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
            'shipping_province' => 'required|string',
            'shipping_district' => 'required|string',
            'shipping_address_detail' => 'nullable|string',
            'total_weight' => 'required|integer',
            'total_price' => 'required|integer',
        ];
    }
}
