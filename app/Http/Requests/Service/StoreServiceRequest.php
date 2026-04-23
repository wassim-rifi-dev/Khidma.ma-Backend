<?php

namespace App\Http\Requests\Service;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'city' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'required|numeric|gte:price_min',
            'cover_image' => 'required|image|mimes:jpg,jpeg,png|max:10240',
            'gallery_images' => 'nullable|array|max:4',
            'gallery_images.*' => 'image|mimes:jpg,jpeg,png|max:10240',
        ];
    }
}
