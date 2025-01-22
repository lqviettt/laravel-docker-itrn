<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            "name" => "required|string|max:32",
            "code" => [
                "required",
                "string",
                "max:20",
                Rule::unique('products', 'code')->ignore($productId)
            ],
            "quantity" => "nullable|integer|min:0",
            "category_id" => "required|exists:categories,id",
            "description" => "nullable|string|max:255",
            "price" => "required|integer",
            "weight" => "nullable|integer",
            "status" => "nullable|integer",
        ];
    }
}
