<?php

namespace App\Http\Requests\Pharmacy;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePharmacyRequest extends FormRequest
{
    /**
     * Only super-admin can create pharmacies globally.
     * Pharmacy owners are created by the admin and assigned later.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('super-admin');
        //return true;
    }

    protected function prepareForValidation(): void
    {
        $value = $this->input('pharmacy_name');
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->merge(['pharmacy_name' => $decoded]);
            }
        }

        if (is_array($this->input('pharmacy_name'))) {
            $this->merge([
                'pharmacy_name' => array_map('trim', $this->input('pharmacy_name')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            // ── Translatable: pharmacy_name ───────────────────
            // Stored as JSON: {"en": "...", "ar": "..."}
            'pharmacy_name'       => ['required', 'array'],
            'pharmacy_name.en'    => ['required', 'string', 'min:2', 'max:150'],
            'pharmacy_name.ar'    => ['required', 'string', 'min:2', 'max:150'],

            // ── Logo image ────────────────────────────────────
            'image'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'pharmacy_name.required'    => __('pharmacy.validation.pharmacy_name.required'),
            'pharmacy_name.en.required' => __('pharmacy.validation.pharmacy_name_en.required'),
            'pharmacy_name.ar.required' => __('pharmacy.validation.pharmacy_name_ar.required'),
            'pharmacy_name.en.min'      => __('pharmacy.validation.pharmacy_name_en.min'),
            'pharmacy_name.ar.min'      => __('pharmacy.validation.pharmacy_name_ar.min'),
            'pharmacy_name.en.max'      => __('pharmacy.validation.pharmacy_name_en.max'),
            'pharmacy_name.ar.max'      => __('pharmacy.validation.pharmacy_name_ar.max'),
            'image.image'               => __('pharmacy.validation.image.image'),
            'image.max'                 => __('pharmacy.validation.image.max'),
        ];
    }
}
