<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20|',
            'role' => 'required|string|in:client,professional',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
            'user_id'  => 'nullable|integer|exists:users,id',
            'category' => 'nullable|string|in:plumbing,electrical,painting,carpentry,ac_repair,appliance',
            'city'     => 'nullable|string|max:100',
        ];
    }
}
