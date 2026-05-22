<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\NearbyPharmaciesNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SendNearbyPharmaciesJob
 *
 * Dispatched from NotificationController::nearbyPharmacies().
 * Runs in the background queue — does not block the API response.
 *
 * Uses User::nearbyPharmacies() or User::nearbyPharmaciesWithMedicine()
 * which chain Pharmacy::nearby() + Branch::nearby() (Haversine) together.
 *
 * On completion sends NearbyPharmaciesNotification to the client.
 * Client reads it via GET /api/v1/me/notifications.
 *
 * ──────────────────────────────────────────────────────────────
 * Dispatch from controller:
 * ──────────────────────────────────────────────────────────────
 *   // General location browsing
 *   SendNearbyPharmaciesJob::dispatch(
 *       userId:   $user->id,
 *       lat:      30.0444,
 *       lng:      31.2357,
 *       radiusKm: 5,
 *   );
 *
 *   // Searching for a specific medicine
 *   SendNearbyPharmaciesJob::dispatch(
 *       userId:       $user->id,
 *       lat:          30.0444,
 *       lng:          31.2357,
 *       radiusKm:     5,
 *       medicineId:   5,
 *       medicineName: 'Paracetamol',
 *   );
 */
class SendNearbyPharmaciesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int         $userId,
        public readonly float       $lat,
        public readonly float       $lng,
        public readonly int         $radiusKm    = 5,
        public readonly int|null    $medicineId  = null,
        public readonly string|null $medicineName = null,
    ) {}

    public function handle(): void
    {
        $user = User::find($this->userId);

        // Only process for valid client users
        if (!$user || !$user->isClient()) {
            return;
        }

        // Resolve pharmacies using the User model location helpers
        // These chain Pharmacy + Branch scopes and eager load branches.city
        $query = $this->medicineId
            ? $user->nearbyPharmaciesWithMedicine(
                $this->lat,
                $this->lng,
                $this->medicineId,
                $this->radiusKm
            )
            : $user->nearbyPharmacies(
                $this->lat,
                $this->lng,
                $this->radiusKm
            );

        $pharmacies = $query->get();

        if ($pharmacies->isEmpty()) {
            Log::info('SendNearbyPharmaciesJob: no results', [
                'user_id'     => $this->userId,
                'lat'         => $this->lat,
                'lng'         => $this->lng,
                'radius_km'   => $this->radiusKm,
                'medicine_id' => $this->medicineId,
            ]);
            return;
        }

        $user->notify(new NearbyPharmaciesNotification(
            pharmacies: $pharmacies,
            lat: $this->lat,
            lng: $this->lng,
            radiusKm: $this->radiusKm,
            medicineId: $this->medicineId,
            medicineName: $this->medicineName,
        ));

        Log::info('SendNearbyPharmaciesJob: notification sent', [
            'user_id'   => $this->userId,
            'count'     => $pharmacies->count(),
            'radius_km' => $this->radiusKm,
        ]);
    }
}
