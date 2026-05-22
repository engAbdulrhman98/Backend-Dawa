<?php

namespace App\Http\Requests\Branch;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    /**
     * Only super-admin and pharmacy-owner can create branches.
     * Pharmacy owner can only create branches for their own pharmacy.
     */
    public function authorize(): bool
    {
        if ($this->user()->hasRole('super-admin')) {
            return true;
        }

        if ($this->user()->hasRole('pharmacy-owner')) {
            // Owner can only create a branch under their own pharmacy
            return (int) $this->input('pharmacy_id') === (int) $this->user()->pharmacy_id;
        }

        return false;
    }

    protected function prepareForValidation(): void
    {
        foreach (['branch_name', 'branch_address', 'branch_phone'] as $field) {
            $value = $this->input($field);

            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $this->merge([$field => $decoded]);
                }
            }

            if (is_array($this->input($field))) {
                $this->merge([
                    $field => array_map('trim', $this->input($field)),
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            // ── Required FKs ─────────────────────────────────
            'pharmacy_id'          => ['required', 'integer', 'exists:pharmacies,id'],
            'city_id'              => ['required', 'integer', 'exists:cities,id'],

            // ── Translatable: branch_name ─────────────────────
            // Stored as JSON: {"en": "...", "ar": "..."}
            'branch_name'          => ['required', 'array'],
            'branch_name.en'       => ['required', 'string', 'min:2', 'max:150'],
            'branch_name.ar'       => ['required', 'string', 'min:2', 'max:150'],

            // ── Translatable: branch_address ──────────────────
            'branch_address'       => ['required', 'array'],
            'branch_address.en'    => ['required', 'string', 'min:5', 'max:255'],
            'branch_address.ar'    => ['required', 'string', 'min:5', 'max:255'],

            // ── Translatable: branch_phone (optional) ─────────
            'branch_phone'         => ['nullable', 'array'],
            'branch_phone.en'      => ['nullable', 'string', 'max:20'],
            'branch_phone.ar'      => ['nullable', 'string', 'max:20'],

            // ── Coordinates (optional) ────────────────────────
            // If not provided, spatie/geocoder auto-fills them
            // from branch_address via Branch::booted() on save.
            'latitude'             => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'            => ['nullable', 'numeric', 'between:-180,180'],

            // ── Branch image ──────────────────────────────────
            'image'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'pharmacy_id.required'       => __('branch.validation.pharmacy_id.required'),
            'pharmacy_id.exists'         => __('branch.validation.pharmacy_id.exists'),
            'city_id.required'           => __('branch.validation.city_id.required'),
            'city_id.exists'             => __('branch.validation.city_id.exists'),
            'branch_name.required'       => __('branch.validation.branch_name.required'),
            'branch_name.en.required'    => __('branch.validation.branch_name_en.required'),
            'branch_name.ar.required'    => __('branch.validation.branch_name_ar.required'),
            'branch_name.en.min'         => __('branch.validation.branch_name_en.min'),
            'branch_name.ar.min'         => __('branch.validation.branch_name_ar.min'),
            'branch_name.en.max'         => __('branch.validation.branch_name_en.max'),
            'branch_name.ar.max'         => __('branch.validation.branch_name_ar.max'),
            'branch_address.required'    => __('branch.validation.branch_address.required'),
            'branch_address.en.required' => __('branch.validation.branch_address_en.required'),
            'branch_address.ar.required' => __('branch.validation.branch_address_ar.required'),
            'branch_address.en.min'      => __('branch.validation.branch_address_en.min'),
            'branch_address.ar.min'      => __('branch.validation.branch_address_ar.min'),
            'branch_phone.en.max'        => __('branch.validation.branch_phone_en.max'),
            'branch_phone.ar.max'        => __('branch.validation.branch_phone_ar.max'),
            'latitude.between'           => __('branch.validation.latitude.between'),
            'longitude.between'          => __('branch.validation.longitude.between'),
            'image.image'                => __('branch.validation.image.image'),
            'image.max'                  => __('branch.validation.image.max'),
        ];
    }
}
