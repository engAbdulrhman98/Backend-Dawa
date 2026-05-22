<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GovernorateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'country_id'       => $this->country_id,
            'governorate_name' => $this->getTranslations('governorate_name'),
            'name'             => $this->getTranslation(
                'governorate_name',
                app()->getLocale(),
                useFallbackLocale: true
            ),
            'governorate_slug' => $this->governorate_slug,
            'country'          => new CountryResource($this->whenLoaded('country')),
            'country_name'     => $this->country?->getTranslation(
                'country_name',
                app()->getLocale(),
                useFallbackLocale: true
            ),
            'created_at'       => $this->created_at?->toISOString(),
            'updated_at'       => $this->updated_at?->toISOString(),
        ];
    }
}
