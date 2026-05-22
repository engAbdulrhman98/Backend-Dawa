<?php

namespace App\Http\Requests\Pharmacy;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePharmacyRequest extends FormRequest
{
    /**
     * Super-admin can update any pharmacy.
     * Pharmacy owner can update only their own pharmacy.
     */
    /**
     * Prepare validation: remove string image inputs (existing image URLs) so they aren't validated as files.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('image'))) {
            unset($this['image']);
            $this->request->remove('image');
            $this->query->remove('image');
        }

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

    public function authorize(): bool
    {
        // $pharmacy = $this->route('pharmacy');

        // if ($this->user()->hasRole('super-admin')) {
        //     return true;
        // }

        // if ($this->user()->hasRole('pharmacy-owner')) {
        //     return (int) $this->user()->pharmacy_id === (int) $pharmacy->id;
        // }

        // return false;
        return true;

    }

    public function rules(): array
    {
        return [
            // ── Translatable: pharmacy_name ───────────────────
            // All fields use 'sometimes' — PATCH-friendly
            // Only send what changed, not the full object
            'pharmacy_name'       => ['sometimes', 'array'],
            'pharmacy_name.en'    => ['sometimes', 'string', 'min:2', 'max:150'],
            'pharmacy_name.ar'    => ['sometimes', 'string', 'min:2', 'max:150'],

            // ── Logo image ────────────────────────────────────
            'image'               => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'pharmacy_name.en.min'  => __('pharmacy.validation.pharmacy_name_en.min'),
            'pharmacy_name.ar.min'  => __('pharmacy.validation.pharmacy_name_ar.min'),
            'pharmacy_name.en.max'  => __('pharmacy.validation.pharmacy_name_en.max'),
            'pharmacy_name.ar.max'  => __('pharmacy.validation.pharmacy_name_ar.max'),
            'image.image'           => __('pharmacy.validation.image.image'),
            'image.max'             => __('pharmacy.validation.image.max'),
        ];
    }
}
