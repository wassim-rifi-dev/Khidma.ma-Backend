<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'role' => 'required|string|in:client,professional',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
            'category' => [
                'nullable',
                'required_if:role,professional',
                'string',
                Rule::exists('categories', 'name'),
            ],
            'city' => 'nullable|required_if:role,professional|string|max:100',
            'description' => 'nullable|string|max:2000',
        ];
    }

    protected function prepareForValidation(): void
    {
        $category = $this->input('category');

        $mappedCategories = [
            'plumbing' => 'Plumbing',
            'electrical' => 'Electrical',
            'painting' => 'Painting',
            'carpentry' => 'Carpentry',
            'ac_repair' => 'AC Repair',
            'appliance' => 'Appliance',
        ];

        if (is_string($category) && isset($mappedCategories[$category])) {
            $this->merge([
                'category' => $mappedCategories[$category],
            ]);
        }
    }
}
