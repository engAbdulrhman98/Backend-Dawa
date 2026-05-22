<?php

namespace App\Http\Requests\Medicine;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMedicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only super admins and pharmacy owners can create medicines globally
        return $this->user()?->hasAnyRole(['super-admin', 'pharmacy-owner']) ?? false;
    }

    protected function prepareForValidation(): void
    {
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

        // If medicine_name was not sent at all, force it to empty array so validation catches it
        if (!$this->has('medicine_name')) {
            $this->merge(['medicine_name' => []]);
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
            'category_id'                    => ['required', 'integer', 'exists:categories,id'],

            // ── Translatable: medicine_name ───────────────────
            'medicine_name'                  => ['required', 'array'],
            'medicine_name.en'               => ['required', 'string', 'min:2', 'max:150'],
            'medicine_name.ar'               => ['required', 'string', 'min:2', 'max:150'],

            // ── Translatable: medicine_description (optional) ─
            'medicine_description'           => ['nullable', 'array'],
            'medicine_description.en'        => ['nullable', 'string', 'max:1000'],
            'medicine_description.ar'        => ['nullable', 'string', 'max:1000'],

            // ── Translatable: medicine_usage (optional) ───────
            'medicine_usage'                 => ['nullable', 'array'],
            'medicine_usage.en'              => ['nullable', 'string', 'max:1000'],
            'medicine_usage.ar'              => ['nullable', 'string', 'max:1000'],

            // ── Translatable: medicine_side_effects (optional) ─
            'medicine_side_effects'          => ['nullable', 'array'],
            'medicine_side_effects.en'       => ['nullable', 'string', 'max:1000'],
            'medicine_side_effects.ar'       => ['nullable', 'string', 'max:1000'],

            // ── Price ─────────────────────────────────────────
            'medicine_price'                 => ['required', 'numeric', 'min:0', 'max:99999.99'],

            // ── Media ─────────────────────────────────────────
            'image'                          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // ── Branches Association ──────────────────────────
            'branches'                       => ['sometimes', 'array'],
            'branches.*'                     => ['integer', 'exists:branches,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required'           => __('medicine.validation.category_id.required'),
            'category_id.exists'             => __('medicine.validation.category_id.exists'),
            'medicine_name.required'         => __('medicine.validation.medicine_name.required'),
            'medicine_name.en.required'      => __('medicine.validation.medicine_name_en.required'),
            'medicine_name.ar.required'      => __('medicine.validation.medicine_name_ar.required'),
            'medicine_name.en.min'           => __('medicine.validation.medicine_name_en.min'),
            'medicine_name.ar.min'           => __('medicine.validation.medicine_name_ar.min'),
            'medicine_name.en.max'           => __('medicine.validation.medicine_name_en.max'),
            'medicine_name.ar.max'           => __('medicine.validation.medicine_name_ar.max'),
            'medicine_price.required'        => __('medicine.validation.medicine_price.required'),
            'medicine_price.numeric'         => __('medicine.validation.medicine_price.numeric'),
            'medicine_price.min'             => __('medicine.validation.medicine_price.min'),
            'image.image'                    => __('medicine.validation.image.image'),
            'image.max'                      => __('medicine.validation.image.max'),
        ];
    }
}
