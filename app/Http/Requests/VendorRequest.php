<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Change to true if any authenticated user can create a vendor
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
            'vendorName' => 'nullable|string|max:255',
            'shopName' => 'nullable|string|max:255',
            'licenseNumber' => 'nullable|string|max:100',
            'nid' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phoneNumber' => 'nullable|string|max:30',
            'vendor_type' => 'nullable|string|max:255',
            'image' => 'nullable|file|mimes:png,jpg,jpeg,webp,avif|max:5120',
            'cvrimage' => 'nullable|file|mimes:png,jpg,jpeg,webp,avif|max:5120',
            'description' => 'nullable|string',
            'documents' => 'nullable',
            'short_description' => 'nullable|string|max:1000',
            'type' => 'nullable|in:inhouse_shop,vendor_shop',
            'status' => 'nullable|in:active,inactive,deactivated',
            'order_number' => 'nullable|integer',
            'old_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable',
            'new_password_confirmation' => 'nullable|same:new_password',
        ];
    }
}

