<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * - super-admin     → can create any role
     * - pharmacy-owner  → can create branch-manager for own pharmacy only
     * - client          → self-registers (no auth) via /api/v1/auth/register
     */
    public function authorize(): bool
    {
        // Client self-registration requires no auth
        if ($this->input('role') === 'client') {
            return true;
        }

        $user = $this->user();

        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isPharmacyOwner() && $this->input('role') === 'branch-manager') {
            return (int) $this->input('pharmacy_id') === (int) $user->pharmacy_id;
        }

        return false;
    }

    public function rules(): array
    {
        $role = $this->input('role');

        return [
            'name'     => ['required', 'string', 'min:2', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'role'     => ['required', 'string', 'in:super-admin,pharmacy-owner,branch-manager,client'],

            // Required for pharmacy-owner and branch-manager, prohibited for others
            'pharmacy_id' => [
                in_array($role, ['pharmacy-owner', 'branch-manager']) ? 'required' : 'prohibited',
                'integer',
                'exists:pharmacies,id',
            ],

            // Required only for branch-manager
            'branch_id' => [
                $role === 'branch-manager' ? 'required' : 'prohibited',
                'integer',
                'exists:branches,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'            => __('user.validation.name.required'),
            'name.min'                 => __('user.validation.name.min'),
            'email.required'           => __('user.validation.email.required'),
            'email.email'              => __('user.validation.email.email'),
            'email.unique'             => __('user.validation.email.unique'),
            'password.required'        => __('user.validation.password.required'),
            'password.confirmed'       => __('user.validation.password.confirmed'),
            'role.required'            => __('user.validation.role.required'),
            'role.in'                  => __('user.validation.role.in'),
            'pharmacy_id.required'     => __('user.validation.pharmacy_id.required'),
            'pharmacy_id.exists'       => __('user.validation.pharmacy_id.exists'),
            'pharmacy_id.prohibited'   => __('user.validation.pharmacy_id.prohibited'),
            'branch_id.required'       => __('user.validation.branch_id.required'),
            'branch_id.exists'         => __('user.validation.branch_id.exists'),
            'branch_id.prohibited'     => __('user.validation.branch_id.prohibited'),
        ];
    }
}
