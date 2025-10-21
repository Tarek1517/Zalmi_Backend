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
        return false;
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
			'city_id' => 'required',
            'area_id' => 'required',
			'address' => 'required|string|max:255',
            'sub_total' => 'required',
            'grand_total' => 'nullable',
			'shipping_charge' => 'nullable|numeric',
            'payment_method' => 'required|string|max:255',
            'payment_status' => 'nullable|in:paid,pending,cancelled',
            'order_code' => 'nullable',
            'order_items' => 'required|array',
        ];
    }
}
