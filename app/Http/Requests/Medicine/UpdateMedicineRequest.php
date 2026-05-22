<?php

namespace App\Http\Requests\Medicine;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateMedicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only super admins and pharmacy owners can update global medicine records
        return $this->user()?->hasAnyRole(['super-admin', 'pharmacy-owner']) ?? false;
    }

    protected function prepareForValidation(): void
    {
        // If image is a string (e.g. existing image URL), remove it so it's not validated as an uploaded file
        if (is_string($this->input('image'))) {
            unset($this['image']);
            $this->request->remove('image');
            $this->query->remove('image');
        }

        foreach (['medicine_name', 'medicine_description', 'medicine_usage', 'medicine_side_effects'] as $field) {
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

        $branches = $this->input('branches');
        if (is_string($branches)) {
            $decoded = json_decode($branches, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->merge(['branches' => $decoded]);
            }
        }
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => __('medicine.validation.medicine_name.required'),
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    public function rules(): array
    {
        return [
            // ── Category ──────────────────────────────────────
            'category_id'                    => ['sometimes', 'integer', 'exists:categories,id'],

            // ── Translatable: medicine_name ───────────────────
            'medicine_name'                  => ['sometimes', 'array'],
            'medicine_name.en'               => ['sometimes', 'string', 'min:2', 'max:150'],
            'medicine_name.ar'               => ['sometimes', 'string', 'min:2', 'max:150'],

            // ── Translatable: medicine_description ────────────
            'medicine_description'           => ['sometimes', 'nullable', 'array'],
            'medicine_description.en'        => ['sometimes', 'nullable', 'string', 'max:1000'],
            'medicine_description.ar'        => ['sometimes', 'nullable', 'string', 'max:1000'],

            // ── Translatable: medicine_usage ──────────────────
            'medicine_usage'                 => ['sometimes', 'nullable', 'array'],
            'medicine_usage.en'              => ['sometimes', 'nullable', 'string', 'max:1000'],
            'medicine_usage.ar'              => ['sometimes', 'nullable', 'string', 'max:1000'],

            // ── Translatable: medicine_side_effects ───────────
            'medicine_side_effects'          => ['sometimes', 'nullable', 'array'],
            'medicine_side_effects.en'       => ['sometimes', 'nullable', 'string', 'max:1000'],
            'medicine_side_effects.ar'       => ['sometimes', 'nullable', 'string', 'max:1000'],

            // ── Price ─────────────────────────────────────────
            'medicine_price'                 => ['sometimes', 'numeric', 'min:0', 'max:99999.99'],

            // ── Media ─────────────────────────────────────────
            'image'                          => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // ── Branches Association ──────────────────────────
            'branches'                       => ['sometimes', 'array'],
            'branches.*'                     => ['integer', 'exists:branches,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists'             => __('medicine.validation.category_id.exists'),
            'medicine_name.en.min'           => __('medicine.validation.medicine_name_en.min'),
            'medicine_name.ar.min'           => __('medicine.validation.medicine_name_ar.min'),
            'medicine_name.en.max'           => __('medicine.validation.medicine_name_en.max'),
            'medicine_name.ar.max'           => __('medicine.validation.medicine_name_ar.max'),
            'medicine_price.numeric'         => __('medicine.validation.medicine_price.numeric'),
            'medicine_price.min'             => __('medicine.validation.medicine_price.min'),
            'image.image'                    => __('medicine.validation.image.image'),
            'image.max'                      => __('medicine.validation.image.max'),
        ];
    }
}
