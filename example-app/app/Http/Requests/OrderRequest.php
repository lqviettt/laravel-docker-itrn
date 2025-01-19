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
            'customer_email' => 'nullable|email',
            'status' => 'required|in:pending,shipping,delivered,canceled',
            'shipping_province' => 'required|string|max:70',
            'shipping_district' => 'required|string|max:70',
            'shipping_ward' => 'nullable|string|max:70',
            'shipping_fee'  => 'nullable|numeric',
            'total_price' => 'nullable|numeric',
            'payment_method' => 'nullable|string',
            'shipping_address_detail' => 'nullable|string|max:70',
            'order_item' => 'required|array',
            'order_item.*.product_id' => 'required|exists:products,id',
            'order_item.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'order_item.*.quantity' => 'required|integer|min:1',
            'order_item.*.price' => 'required|integer',
        ];
    }

    function generate_rand_code()
    {
        $strRandom = uniqid();
        return strtoupper(substr($strRandom, 6, 6));
    }

    public function storeOrder()
    {
        $nameParts = explode(' ', $this->customer_name);
        $firstname = array_pop($nameParts);
        $lastname = implode(' ', $nameParts);

        return [
            'code' => $this->generate_rand_code(),
            'firstname' => $firstname,
            'lastname' => $lastname,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'shipping_province' => $this->shipping_province,
            'shipping_district' => $this->shipping_district,
            'shipping_ward' => $this->shipping_ward,
            'shipping_address_detail' => $this->shipping_address_detail,
            'shipping_fee' => $this->shipping_fee,
            'total_price' => $this->total_price,
            'payment_method' => $this->payment_method,
            'status' => $this->status ?? 'default_status',
        ];
    }

    public function updateOrder()
    {
        $nameParts = explode(' ', $this->customer_name);
        $firstname = array_pop($nameParts);
        $lastname = implode(' ', $nameParts);

        return [
            'code' => $this->code,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'shipping_province' => $this->shipping_province,
            'shipping_district' => $this->shipping_district,
            'shipping_ward' => $this->shipping_ward,
            'shipping_address_detail' => $this->shipping_address_detail,
            'shipping_fee' => $this->shipping_fee,
            'total_price' => $this->total_price,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'order_item' => $this->order_item
        ];
    }
}
