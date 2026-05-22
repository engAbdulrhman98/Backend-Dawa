<?php

namespace App\Http\Requests\Governorate;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateGovernorateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('super-admin') ?? false;
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('governorate_name') && is_string($this->governorate_name)) {
            $this->merge([
                'governorate_name' => json_decode($this->governorate_name, true) ?? [],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'governorate_name' => ['required', 'array'],
            'governorate_name.en' => ['required', 'string', 'max:255'],
            'governorate_name.ar' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'governorate_name.required' => __('governorates.validation.name_required'),
            'governorate_name.array' => __('governorates.validation.name_required'),
            'governorate_name.en.required' => __('governorates.validation.name_en_required'),
            'governorate_name.en.string' => __('governorates.validation.name_en_required'),
            'governorate_name.en.max' => __('governorates.validation.name_en_max'),
            'governorate_name.ar.required' => __('governorates.validation.name_ar_required'),
            'governorate_name.ar.string' => __('governorates.validation.name_ar_required'),
            'governorate_name.ar.max' => __('governorates.validation.name_ar_max'),
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json([
                'message' => __('governorates.validation.failed'),
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
