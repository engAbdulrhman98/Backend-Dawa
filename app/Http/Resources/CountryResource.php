<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'country_name' => $this->getTranslations('country_name'),          // full translations array
            'name' => $this->getTranslation(        // translated for current locale
                    'country_name',
                    app()->getLocale(),
                    useFallbackLocale: true
                ),
            'country_slug' => $this->country_slug,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
