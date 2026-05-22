<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineResource extends JsonResource
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
            // ── Identity ──────────────────────────────────────
            'id'           => $this->id,
            'slug'         => $this->medicine_slug,
            'category_id'  => $this->category_id,

            // ── Translatable fields ───────────────────────────
            // Returns all translations: { "en": "...", "ar": "..." }
            'medicine_name' => $this->getTranslations('medicine_name'),
            'name'          => $this->getTranslation('medicine_name', app()->getLocale(), useFallbackLocale: true),
            
            'medicine_description' => $this->getTranslations('medicine_description'),
            'description'   => $this->getTranslation('medicine_description', app()->getLocale(), useFallbackLocale: true),

            'medicine_usage' => $this->getTranslations('medicine_usage'),
            'medicine_side_effects' => $this->getTranslations('medicine_side_effects'),
            // ── Price ─────────────────────────────────────────
            // Single price on the medicine — no average_price
            'price'        => $this->medicine_price,

            // ── Stock ─────────────────────────────────────────
            // Total quantity summed across all branches
            'total_stock'  => $this->total_stock,

            // ── Media ─────────────────────────────────────────
            'image'        => $this->getFirstMediaUrl('medicines'),

            // ── Timestamps ────────────────────────────────────
            'created_at'   => $this->created_at?->toDateTimeString(),
            'updated_at'   => $this->updated_at?->toDateTimeString(),

            // ── Relationships (only when ?include= is passed) ──
            'category'     => new CategoryResource($this->whenLoaded('category')),
            'branches'     => BranchResource::collection($this->whenLoaded('branches')),

            // ── Pivot data ────────────────────────────────────
            // Appears automatically when medicine is accessed
            // through a branch (branch->medicines) showing
            // that branch's specific quantity, price, expiry_date
            'pivot'        => $this->when(isset($this->pivot), [
                'quantity'    => $this->pivot?->quantity,
                'price'       => $this->pivot?->price,
                'expiry_date' => $this->pivot?->expiry_date,
            ]),
        ];
    }
}
