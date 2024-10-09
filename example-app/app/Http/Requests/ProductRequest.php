<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            "name" => "required|string|max:32",
            "code" => "required|string|unique:products,code", 
            "category_id" => "required|exists:categories,id", 
            "description" => "nullable|string",
            "price" => "required|numeric", 
            "status" => "nullable|string", 
        ];
    }    
}
