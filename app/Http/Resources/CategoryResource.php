<?php

namespace App\Http\Resources;

use App\Http\Resources\MedicineResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'category_name' => $this->getTranslations('category_name'),
            'category_description' => $this->getTranslations('category_description'),
            'name' => $this->getTranslation('category_name', $locale),
            'description' => $this->getTranslation('category_description', $locale),
            'category_slug' => $this->category_slug,
            'slug' => $this->category_slug,

            'medicines_count' => $this->whenCounted('medicines'),
            'medicines' => MedicineResource::collection($this->whenLoaded('medicines')),

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
