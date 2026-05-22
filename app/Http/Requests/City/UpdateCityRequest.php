<?php

namespace App\Http\Requests\City;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('super-admin') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('city_name') && is_string($this->city_name)) {
            $this->merge([
                'city_name' => json_decode($this->city_name, true) ?? [],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'city_name' => ['required', 'array'],
            'city_name.en' => ['required', 'string', 'max:255'],
            'city_name.ar' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'city_name.required' => __('cities.validation.name_required'),
            'city_name.array' => __('cities.validation.name_required'),
            'city_name.en.required' => __('cities.validation.name_en_required'),
            'city_name.en.string' => __('cities.validation.name_en_required'),
            'city_name.en.max' => __('cities.validation.name_en_max'),
            'city_name.ar.required' => __('cities.validation.name_ar_required'),
            'city_name.ar.string' => __('cities.validation.name_ar_required'),
            'city_name.ar.max' => __('cities.validation.name_ar_max'),
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json([
                'message' => __('cities.validation.failed'),
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
