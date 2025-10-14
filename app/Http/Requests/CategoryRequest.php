<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'slug' => 'nullable',
            'parent_id' => 'nullable|integer',
            'icon' => 'nullable|string|max:1000',
            'banner' => 'nullable|mimes:png,jpg,jpeg,webp,avif',
            'short_description' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|min:0',
            'order_number' => 'required|integer',
            'status' => 'required',
        ];
    }
    protected $stopOnFirstFailure = true;
}
