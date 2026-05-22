<?php

namespace App\Notifications;

use App\Models\Pharmacy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

/**
 * NearbyPharmaciesNotification
 *
 * Sent to a client after SendNearbyPharmaciesJob resolves their
 * nearby pharmacies query in the background queue.
 *
 * Recipients:
 *   - Client / customer users only
 *
 * Triggered by:
 *   - SendNearbyPharmaciesJob (dispatched from NotificationController::nearbyPharmacies)
 *
 * Channels:
 *   - database  → in-app notification, read via GET /me/notifications
 *   - mail      → only when searching for a specific medicine (not for general browsing)
 */
class NearbyPharmaciesNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Collection  $pharmacies,
        public readonly float       $lat,
        public readonly float       $lng,
        public readonly int         $radiusKm    = 5,
        public readonly int|null    $medicineId  = null,
        public readonly string|null $medicineName = null,
    ) {}

    /**
     * Send mail only when searching for a specific medicine.
     * General location browsing is database-only (less noisy).
     */
    public function via(object $notifiable): array
    {
        return $this->medicineId
            ? ['database', 'mail']
            : ['database'];
    }

    // ──────────────────────────────────────────
    // Mail
    // ──────────────────────────────────────────

    public function toMail(object $notifiable): MailMessage
    {
        $count = $this->pharmacies->count();

        $subject = $this->medicineName
            ? "✅ {$count} pharmacies near you have {$this->medicineName} in stock"
            : "📍 {$count} pharmacies found near you";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line(
                $this->medicineName
                    ? "We found {$count} pharmacies within {$this->radiusKm}km that have **{$this->medicineName}** in stock."
                    : "We found {$count} pharmacies within {$this->radiusKm}km of your location."
            );

        // List up to 5 in the email body
        $this->pharmacies->take(5)->each(function (Pharmacy $pharmacy) use ($mail) {
            $name   = $pharmacy->getTranslation('pharmacy_name', 'en')
                ?? $pharmacy->getTranslation('pharmacy_name', 'ar');

            // Guard: branches must be loaded by SendNearbyPharmaciesJob
            $branch = $pharmacy->relationLoaded('branches')
                ? $pharmacy->branches->first()
                : null;

            $city = $branch?->relationLoaded('city')
                ? ($branch->city?->getTranslation('city_name', 'en') ?? '')
                : '';

            $dist = isset($branch->distance)
                ? round((float) $branch->distance, 1) . ' km away'
                : '';

            $mail->line("• **{$name}** — {$city} {$dist}");
        });

        if ($count > 5) {
            $mail->line('...and ' . ($count - 5) . ' more.');
        }

        return $mail->salutation('Pharmacy Management System');
    }

    // ──────────────────────────────────────────
    // Database
    // ──────────────────────────────────────────

    /**
     * Stored in the notifications table as JSON.
     * Shaped by NotificationResource::shapeNearbyPharmacies() for API responses.
     *
     * Includes up to 20 pharmacies with their nearest branch data.
     * branches relation must be loaded before this is called
     * (guaranteed by SendNearbyPharmaciesJob via User::nearbyPharmacies()).
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type'          => 'nearby_pharmacies',
            'lat'           => $this->lat,
            'lng'           => $this->lng,
            'radius_km'     => $this->radiusKm,
            'medicine_id'   => $this->medicineId,
            'medicine_name' => $this->medicineName,
            'count'         => $this->pharmacies->count(),
            'pharmacies'    => $this->pharmacies
                ->take(20)
                ->map(function (Pharmacy $pharmacy) {
                    // Guard: branches must be eager loaded
                    $nearestBranch = $pharmacy->relationLoaded('branches')
                        ? $pharmacy->branches->first()
                        : null;

                    return [
                        'id'   => $pharmacy->id,
                        'slug' => $pharmacy->pharmacy_slug,
                        'name' => $pharmacy->getTranslations('pharmacy_name'),
                        'logo' => $pharmacy->getFirstMediaUrl('pharmacies'),

                        'nearest_branch' => $nearestBranch ? [
                            'id'   => $nearestBranch->id,
                            'slug' => $nearestBranch->branch_slug,
                            'name' => $nearestBranch->getTranslations('branch_name'),

                            // Guard: city must be loaded
                            'city' => $nearestBranch->relationLoaded('city')
                                ? $nearestBranch->city?->getTranslations('city_name')
                                : null,

                            'distance_km' => isset($nearestBranch->distance)
                                ? round((float) $nearestBranch->distance, 2)
                                : null,

                            // These 3 accessors are generated from lat/lng — no DB column needed
                            'map_url'        => $nearestBranch->map_url,
                            'directions_url' => $nearestBranch->directions_url,
                        ] : null,
                    ];
                })
                ->values()
                ->toArray(),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
