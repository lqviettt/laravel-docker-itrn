<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;


class OrderRequest extends FormRequest
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
        $orderId = $this->route('order') ? $this->route('order')->id : null;
        $codeRule = $this->isMethod('POST') 
        ? 'nullable|string|max:20'
        : [
            'required',
            'string',
            'max:20',
            Rule::unique('orders', 'code')->ignore($orderId)
        ];
        return [
            "code" => $codeRule,
            'customer_name' => 'required|string|max:32',
            'customer_phone' => 'required|string|max:10',
            'status' => 'required|in:pending,shipping,delivered,canceled',
            'shipping_address' => 'required|string|max:255',
            'order_items' => 'required|array',
            'order_items.*.product_id' => 'required|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.price' => 'required|integer',
        ];
    }

    public function storeOrder()
    {
        return [
            'code' => Str::random(10),
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'shipping_address' => $this->shipping_address,
            'status' => $this->status ?? 'default_status',
        ];
    }

    public function updateOrder()
    {
        return [
            'code' => $this->code,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'shipping_address' => $this->shipping_address,
        ];
    }
}
