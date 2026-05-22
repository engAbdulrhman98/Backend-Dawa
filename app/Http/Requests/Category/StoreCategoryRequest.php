<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('super-admin') ?? false;
    }

    protected function prepareForValidation(): void
    {
        foreach (['category_name', 'category_description'] as $field) {
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

        // If category_name was not sent at all, force it to empty array
        // so array validation rules trigger correctly
        if (!$this->has('category_name')) {
            $this->merge(['category_name' => []]);
        }
    }

    public function rules(): array
    {
        return [
            'category_name' => ['required', 'array', 'min:1'],
            'category_name.en' => ['required', 'string', 'min:2', 'max:255'],
            'category_name.ar' => ['required', 'string', 'min:2', 'max:255'],

            'category_description' => ['nullable', 'array'],
            'category_description.en' => ['nullable', 'string', 'max:2000'],
            'category_description.ar' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_name.required' => __('categories.validation.category_name_required'),
            'category_name.array' => __('categories.validation.category_name_array'),
            'category_name.min' => __('categories.validation.category_name_required'),
            'category_name.en.required' => __('categories.validation.category_name_en_required'),
            'category_name.en.string' => __('categories.validation.category_name_en_string'),
            'category_name.en.min' => __('categories.validation.category_name_en_min'),
            'category_name.en.max' => __('categories.validation.category_name_en_max'),
            'category_name.ar.required' => __('categories.validation.category_name_ar_required'),
            'category_name.ar.string' => __('categories.validation.category_name_ar_string'),
            'category_name.ar.min' => __('categories.validation.category_name_ar_min'),
            'category_name.ar.max' => __('categories.validation.category_name_ar_max'),
        ];
    }

    // Return JSON error response instead of redirect
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => __('categories.validation.category_name_required'),
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
