<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required',
            'email' => 'nullable|email|regex:/(.+)@(.+)\.(.+)/i|max:255',
            'phone' => 'required|string',
            'customer_address_id' => 'nullable|exists:customer_addresses,id',

            'city_id' => 'required_without:customer_address_id|nullable|integer|exists:cities,id',
            'area_id' => 'required_without:customer_address_id|nullable|integer|exists:areas,id',
            'address' => 'required_without:customer_address_id|nullable|string|max:255',

            'sub_total' => 'required|numeric',
            'grand_total' => 'nullable|numeric',
            'shipping_charge' => 'nullable|numeric',
            'payment_method' => 'required|string|max:255',
            'payment_status' => 'nullable|in:paid,pending,cancelled',
            'order_code' => 'nullable|string|max:255',
            'order_items' => 'required|array',
        ];
    }
}
