<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'variant_option_id' => 'required|exists:variant_options,id',
            'value' => 'required|string|max:50',
            'quantity' => 'required|integer',
            'price' => 'required|numeric|min:0',
        ];
    }
}
