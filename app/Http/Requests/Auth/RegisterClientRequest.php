<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Public endpoint — no auth required for self-registration
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Normalize has_branches to boolean
        if ($this->has('has_branches')) {
            $val = $this->input('has_branches');
            if (is_string($val)) {
                $this->merge(['has_branches' => filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)]);
            }
        }
    }

    public function rules(): array
    {
        return [
            // ── Step 1: Shared by all roles ──────────────────────────────────
            'name'                  => ['required', 'string', 'min:2', 'max:100'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'role'                  => ['sometimes', 'string', 'in:client,pharmacy_owner'],

            // ── Step 2: Pharmacy Owner only ──────────────────────────────────
            'pharmacy_name_en'      => ['required_if:role,pharmacy_owner', 'nullable', 'string', 'min:2', 'max:150'],
            'pharmacy_name_ar'      => ['required_if:role,pharmacy_owner', 'nullable', 'string', 'min:2', 'max:150'],

            // Toggle: does the owner have branches? (false = main-only, true = has branches)
            'has_branches'          => ['required_if:role,pharmacy_owner', 'nullable', 'boolean'],

            // City is always required for pharmacy owners (used for main branch or first branch)
            'city_id'               => ['required_if:role,pharmacy_owner', 'nullable', 'integer', 'exists:cities,id'],

            // ── Branch details (only required when has_branches = true) ───────
            'branch_name_en'        => ['required_if:has_branches,1', 'nullable', 'string', 'min:2', 'max:150'],
            'branch_name_ar'        => ['required_if:has_branches,1', 'nullable', 'string', 'min:2', 'max:150'],
            'branch_address_en'     => ['required_if:has_branches,1', 'nullable', 'string', 'min:5', 'max:255'],
            'branch_address_ar'     => ['required_if:has_branches,1', 'nullable', 'string', 'min:5', 'max:255'],
            'branch_phone'          => ['nullable', 'string', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                 => __('auth.validation.name.required'),
            'name.min'                      => __('auth.validation.name.min'),
            'email.required'                => __('auth.validation.email.required'),
            'email.email'                   => __('auth.validation.email.email'),
            'email.unique'                  => __('auth.validation.email.unique'),
            'password.required'             => __('auth.validation.password.required'),
            'password.confirmed'            => __('auth.validation.password.confirmed'),
            'password.min'                  => __('auth.validation.password.min'),
            'pharmacy_name_en.required_if'  => 'Pharmacy name in English is required.',
            'pharmacy_name_ar.required_if'  => 'Pharmacy name in Arabic is required.',
            'has_branches.required_if'      => 'Please specify if your pharmacy has branches.',
            'city_id.required_if'           => 'City is required for pharmacy owners.',
            'city_id.exists'                => 'Selected city does not exist.',
            'branch_name_en.required_if'    => 'Branch name in English is required when adding branches.',
            'branch_name_ar.required_if'    => 'Branch name in Arabic is required when adding branches.',
            'branch_address_en.required_if' => 'Branch address in English is required when adding branches.',
            'branch_address_ar.required_if' => 'Branch address in Arabic is required when adding branches.',
        ];
    }
}
