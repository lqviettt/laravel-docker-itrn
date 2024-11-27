<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VariantRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "type" => "required|string|max:50",
            "name" => "required|string|max:50|unique:variant_options,name",
        ];
    }
}
