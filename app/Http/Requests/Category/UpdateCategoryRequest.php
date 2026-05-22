<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('super-admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'category_name' => ['sometimes', 'array'],
            'category_name.en' => ['sometimes', 'string', 'max:255'],
            'category_name.ar' => ['sometimes', 'string', 'max:255'],

            'category_description' => ['nullable', 'array'],
            'category_description.en' => ['nullable', 'string', 'max:2000'],
            'category_description.ar' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_name.required' => __('categories.validation.category_name_required'),
            'category_name.en.required' => __('categories.validation.category_name_en_required'),
            'category_name.ar.required' => __('categories.validation.category_name_ar_required'),
            'category_name.array' => __('categories.validation.category_name_array'),
            'category_name.en.string' => __('categories.validation.category_name_en_string'),
            'category_name.ar.string' => __('categories.validation.category_name_ar_string'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('category_name') && is_array($this->category_name)) {
            $this->merge([
                'category_name' => array_map('trim', $this->category_name),
            ]);
        }
    }
}
