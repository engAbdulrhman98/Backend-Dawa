<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PharmacyResource extends JsonResource
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
            'id'           => $this->id,
            'slug'         => $this->pharmacy_slug,

            // ── Translatable ──────────────────────────────────
            // Returns all translations: {"en": "...", "ar": "..."}
            // Mobile app picks the locale it needs from the object
            'name'         => $this->getTranslations('pharmacy_name'),
            'pharmacy_name'=> $this->getTranslations('pharmacy_name'),

            // ── Media ─────────────────────────────────────────
            'logo'         => $this->getFirstMediaUrl('pharmacies'),

            // ── Computed accessors (from $appends) ────────────
            // branch_count → cached-aware total branches count
            // has_stock    → bool, true if any medicine is in stock
            'branch_count' => $this->branch_count,
            'has_stock'    => $this->has_stock,

            // ── Timestamps ────────────────────────────────────
            'created_at'   => $this->created_at?->toDateTimeString(),
            'updated_at'   => $this->updated_at?->toDateTimeString(),

            // ── Relationships (only when ?include= is passed) ──
            // ?include=branches
            'branches'     => BranchResource::collection($this->whenLoaded('branches')),

            // ?include=owners
            'owners'       => UserResource::collection($this->whenLoaded('owners')),

            // ?include=managers
            'managers'     => UserResource::collection($this->whenLoaded('managers')),
        ];
    }
}
