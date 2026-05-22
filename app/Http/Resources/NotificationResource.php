<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * NotificationResource
 *
 * Shapes the raw notifications table row into a typed API response.
 *
 * Handles the two notification types in this system:
 *   low_stock          → sent to branch-manager + pharmacy-owner
 *   nearby_pharmacies  → sent to client/customer
 *
 * Notification table columns:
 *   id | type (class FQN) | notifiable_type | notifiable_id
 *   data (JSON) | read_at | created_at | updated_at
 */
class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // data is stored as JSON in DB — decode if still a string
        $data = is_array($this->data)
            ? $this->data
            : json_decode($this->data, true);

        $type = $data['type'] ?? null;

        return [
            'id'         => $this->id,
            'type'       => $type,
            'is_read'    => !is_null($this->read_at),
            'read_at'    => $this->read_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),

            // Typed and shaped payload — each type has its own clean structure
            'data' => match ($type) {
                'low_stock'         => $this->shapeLowStock($data),
                'nearby_pharmacies' => $this->shapeNearbyPharmacies($data),
                default             => $data,
            },
        ];
    }

    // ──────────────────────────────────────────
    // Shapers — one per notification type
    // ──────────────────────────────────────────

    /**
     * Shape data from LowStockNotification::toDatabase()
     *
     * Sent to: branch-manager, pharmacy-owner
     * Keys: medicine_id, medicine_slug, medicine_name,
     *       branch_id, branch_slug, branch_name,
     *       pharmacy_id, pharmacy_name, quantity, threshold
     */
    private function shapeLowStock(array $data): array
    {
        return [
            'medicine' => [
                'id'   => $data['medicine_id']   ?? null,
                'slug' => $data['medicine_slug'] ?? null,
                'name' => $data['medicine_name'] ?? null, // {"en":...,"ar":...}
            ],
            'branch' => [
                'id'   => $data['branch_id']   ?? null,
                'slug' => $data['branch_slug'] ?? null,
                'name' => $data['branch_name'] ?? null,  // {"en":...,"ar":...}
            ],
            'pharmacy' => [
                'id'   => $data['pharmacy_id']   ?? null,
                'name' => $data['pharmacy_name'] ?? null, // {"en":...,"ar":...}
            ],
            'quantity'  => $data['quantity']  ?? null,
            'threshold' => $data['threshold'] ?? null,
        ];
    }

    /**
     * Shape data from NearbyPharmaciesNotification::toDatabase()
     *
     * Sent to: client
     * Keys: lat, lng, radius_km, medicine_id, medicine_name,
     *       count, pharmacies[]
     */
    private function shapeNearbyPharmacies(array $data): array
    {
        return [
            'location' => [
                'lat'       => $data['lat']       ?? null,
                'lng'       => $data['lng']       ?? null,
                'radius_km' => $data['radius_km'] ?? null,
            ],

            // null when doing a general search (no specific medicine)
            'medicine' => isset($data['medicine_id']) ? [
                'id'   => $data['medicine_id'],
                'name' => $data['medicine_name'] ?? null,
            ] : null,

            'count'      => $data['count'] ?? 0,
            'pharmacies' => collect($data['pharmacies'] ?? [])
                ->map(fn(array $p) => [
                    'id'   => $p['id']   ?? null,
                    'slug' => $p['slug'] ?? null,
                    'name' => $p['name'] ?? null, // {"en":...,"ar":...}
                    'logo' => $p['logo'] ?? null,
                    'nearest_branch' => isset($p['nearest_branch']) ? [
                        'id'             => $p['nearest_branch']['id']             ?? null,
                        'slug'           => $p['nearest_branch']['slug']           ?? null,
                        'name'           => $p['nearest_branch']['name']           ?? null,
                        'city'           => $p['nearest_branch']['city']           ?? null,
                        'distance_km'    => $p['nearest_branch']['distance_km']    ?? null,
                        'map_url'        => $p['nearest_branch']['map_url']        ?? null,
                        'directions_url' => $p['nearest_branch']['directions_url'] ?? null,
                    ] : null,
                ])
                ->values()
                ->toArray(),
        ];
    }
}
