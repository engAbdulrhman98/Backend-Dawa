<?php

namespace App\Http\Requests\Branch;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchRequest extends FormRequest
{
    /**
     * Super-admin can update any branch.
     * Pharmacy owner can update only branches of their own pharmacy.
     * Branch manager cannot update branch records (only manages stock).
     */
    public function authorize(): bool
    {
        $branch = $this->route('branch');

        if ($this->user()->hasRole('super-admin')) {
            return true;
        }

        if ($this->user()->hasRole('pharmacy-owner')) {
            return (int) $this->user()->pharmacy_id === (int) $branch->pharmacy_id;
        }

        return false;
    }

    protected function prepareForValidation(): void
    {
        // If image is a string (e.g. existing image URL), remove it so it's not validated as an uploaded file
        if (is_string($this->input('image'))) {
            unset($this['image']);
            $this->request->remove('image');
            $this->query->remove('image');
        }

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
            // ── FKs (optional on update) ──────────────────────
            // 'sometimes' = only validated if present in request
            // This makes the endpoint fully PATCH-compatible
            'pharmacy_id'          => ['sometimes', 'integer', 'exists:pharmacies,id'],
            'city_id'              => ['sometimes', 'integer', 'exists:cities,id'],

            // ── Translatable: branch_name ─────────────────────
            'branch_name'          => ['sometimes', 'array'],
            'branch_name.en'       => ['sometimes', 'string', 'min:2', 'max:150'],
            'branch_name.ar'       => ['sometimes', 'string', 'min:2', 'max:150'],

            // ── Translatable: branch_address ──────────────────
            'branch_address'       => ['sometimes', 'array'],
            'branch_address.en'    => ['sometimes', 'string', 'min:5', 'max:255'],
            'branch_address.ar'    => ['sometimes', 'string', 'min:5', 'max:255'],

            // ── Translatable: branch_phone ────────────────────
            'branch_phone'         => ['sometimes', 'nullable', 'array'],
            'branch_phone.en'      => ['sometimes', 'nullable', 'string', 'max:20'],
            'branch_phone.ar'      => ['sometimes', 'nullable', 'string', 'max:20'],

            // ── Coordinates ───────────────────────────────────
            'latitude'             => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude'            => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],

            // ── Re-geocode flag ───────────────────────────────
            // Pass refresh_coordinates=true after updating address
            // to trigger $branch->refreshCoordinates() via geocoder
            'refresh_coordinates'  => ['sometimes', 'boolean'],

            // ── Branch image ──────────────────────────────────
            'image'                => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'pharmacy_id.exists'         => __('branch.validation.pharmacy_id.exists'),
            'city_id.exists'             => __('branch.validation.city_id.exists'),
            'branch_name.en.min'         => __('branch.validation.branch_name_en.min'),
            'branch_name.ar.min'         => __('branch.validation.branch_name_ar.min'),
            'branch_name.en.max'         => __('branch.validation.branch_name_en.max'),
            'branch_name.ar.max'         => __('branch.validation.branch_name_ar.max'),
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
