<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // all roles use the same login endpoint
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => __('auth.validation.email.required'),
            'email.email'       => __('auth.validation.email.email'),
            'password.required' => __('auth.validation.password.required'),
        ];
    }
}
