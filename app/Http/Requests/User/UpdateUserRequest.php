<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
class UpdateUserRequest extends FormRequest
{
    /**
     * - super-admin     → can update any user
     * - pharmacy-owner  → can update branch-managers of own pharmacy
     * - branch-manager / client → can update only own profile
     */
    public function authorize(): bool
    {
        $authUser   = $this->user();
        $targetUser = $this->route('user') ?? $authUser;

        if ($authUser->isSuperAdmin()) {
            return true;
        }

        if ($authUser->isPharmacyOwner()) {
            return clone $targetUser && (int) $authUser->pharmacy_id === (int) $targetUser->pharmacy_id;
        }

        // Anyone can edit their own profile
        return (int) $authUser->id === (int) $targetUser->id;
    }

    public function rules(): array
    {
        $targetUser = $this->route('user') ?? $this->user();
        $userId = $targetUser?->id;

        return [
            // All fields are optional on update (PATCH-friendly)
            'name'        => ['sometimes', 'string', 'min:2', 'max:100'],
            'email'       => ['sometimes', 'email', "unique:users,email,{$userId}"],
            'password'    => ['sometimes', 'confirmed', Password::min(8)->letters()->numbers()],

            // Only super-admin can reassign these
            'pharmacy_id' => ['sometimes', 'nullable', 'integer', 'exists:pharmacies,id'],
            'branch_id'   => ['sometimes', 'nullable', 'integer', 'exists:branches,id'],

            // Only super-admin can change role
            'role'        => ['sometimes', 'string', 'in:super-admin,pharmacy-owner,branch-manager,client'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.min'           => __('user.validation.name.min'),
            'email.email'        => __('user.validation.email.email'),
            'email.unique'       => __('user.validation.email.unique'),
            'password.confirmed' => __('user.validation.password.confirmed'),
            'role.in'            => __('user.validation.role.in'),
            'pharmacy_id.exists' => __('user.validation.pharmacy_id.exists'),
            'branch_id.exists'   => __('user.validation.branch_id.exists'),
        ];
    }
}
