<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'governorate_id' => $this->governorate_id,
            'city_name' => $this->getTranslations('city_name'),
            'name' => $this->getTranslation(
                'city_name',
                app()->getLocale(),
                useFallbackLocale: true
            ),
            'city_slug' => $this->city_slug,
            'governorate' => new GovernorateResource($this->whenLoaded('governorate')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
