<?php

namespace App\Http\Requests\Country;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // if ($this->user()->hasRole('super-admin')) {
        //     return true;
        // }
        //return true;
        return $this->user()?->hasRole('super-admin') ?? false;
    }

    protected function prepareForValidation(): void
    {
        // Handles JSON string: {"en":"Egypt","ar":"مصر"}
        if ($this->has('country_name') && is_string($this->country_name)) {
            $this->merge([
                'country_name' => json_decode($this->country_name, true) ?? [],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'country_name' => ['required', 'array'],
            'country_name.en' => ['required', 'string', 'max:255'],
            'country_name.ar' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'country_name.required' => __('countries.validation.name_required'),
            'country_name.array' => __('countries.validation.name_required'),
            'country_name.en.required' => __('countries.validation.name_en_required'),
            'country_name.en.string' => __('countries.validation.name_en_required'),
            'country_name.en.max' => __('countries.validation.name_en_max'),
            'country_name.ar.required' => __('countries.validation.name_ar_required'),
            'country_name.ar.string' => __('countries.validation.name_ar_required'),
            'country_name.ar.max' => __('countries.validation.name_ar_max'),
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json([
                'message' => __('countries.validation.failed'),
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
