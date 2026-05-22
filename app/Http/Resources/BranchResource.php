<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // ── Identity ──────────────────────────────────────
            'id'             => $this->id,
            'slug'           => $this->branch_slug,
            'pharmacy_id'    => $this->pharmacy_id,
            'city_id'        => $this->city_id,
            'governorate_id' => $this->governorate_id,

            // ── Translatable fields ───────────────────────────
            // Returns all translations: {"en": "...", "ar": "..."}
            // Mobile app picks the locale it needs
            'name'           => $this->getTranslations('branch_name'),
            'branch_name'    => $this->getTranslations('branch_name'),
            'address'        => $this->getTranslations('branch_address'),
            'branch_address' => $this->getTranslations('branch_address'),
            'phone'          => $this->getTranslations('branch_phone'),
            'branch_phone'   => $this->getTranslations('branch_phone'),

            // ── Location ──────────────────────────────────────
            // Raw coordinates for the map SDK (e.g. Google Maps Flutter)
            'latitude'       => $this->latitude,
            'longitude'      => $this->longitude,

            // ── Google Maps links (generated from lat/lng — no DB column) ──
            // map_url       → deep link to open Google Maps app
            // map_embed_url → webview embed URL
            // directions_url→ navigation link
            'map_url'        => $this->map_url,
            'map_embed_url'  => $this->map_embed_url,
            'directions_url' => $this->directions_url,

            // ── Stock summary ─────────────────────────────────
            'total_stock'    => $this->total_stock,
            'has_low_stock'  => $this->has_low_stock,

            // ── Distance (only present when using nearby() scope) ──
            // Added by selectRaw in scopeNearby() — not always present
            'distance_km'    => $this->when(
                isset($this->distance),
                fn() => round((float) $this->distance, 2)
            ),

            // ── Media ─────────────────────────────────────────
            'image'          => $this->getFirstMediaUrl('branches'),

            // ── Timestamps ────────────────────────────────────
            'created_at'     => $this->created_at?->toDateTimeString(),
            'updated_at'     => $this->updated_at?->toDateTimeString(),

            // ── Relationships (only when ?include= is passed) ──
            'pharmacy'       => new PharmacyResource($this->whenLoaded('pharmacy')),
            'city'           => new CityResource($this->whenLoaded('city')),
            'governorate'    => new GovernorateResource($this->whenLoaded('governorate')),
            'managers'       => UserResource::collection($this->whenLoaded('managers')),
            'medicines'      => MedicineResource::collection($this->whenLoaded('medicines')),
        ];
    }
}
