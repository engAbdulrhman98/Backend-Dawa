<?php

namespace App\Notifications;

use App\Models\Branch;
use App\Models\Medicine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * LowStockNotification
 *
 * Sent when a medicine's quantity in a branch falls at or below the threshold.
 *
 * Recipients:
 *   - Branch manager of the affected branch
 *   - Pharmacy owner of the pharmacy that owns the branch
 *
 * Triggered by:
 *   - CheckLowStockJob (scheduled, runs daily at 08:00)
 *
 * Channels:
 *   - database  → in-app bell icon, read via GET /me/notifications
 *   - mail      → email alert
 */
class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Medicine $medicine,
        public readonly Branch   $branch,
        public readonly int      $quantity,
        public readonly int      $threshold = 10,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    // ──────────────────────────────────────────
    // Mail
    // ──────────────────────────────────────────

    public function toMail(object $notifiable): MailMessage
    {
        // Guard: load pharmacy relation if not already eager loaded
        $pharmacy = $this->branch->relationLoaded('pharmacy')
            ? $this->branch->pharmacy
            : $this->branch->load('pharmacy')->pharmacy;

        $medicineName = $this->medicine->getTranslation('medicine_name', 'en')
            ?? $this->medicine->getTranslation('medicine_name', 'ar');

        $branchName   = $this->branch->getTranslation('branch_name', 'en')
            ?? $this->branch->getTranslation('branch_name', 'ar');

        $pharmacyName = $pharmacy->getTranslation('pharmacy_name', 'en')
            ?? $pharmacy->getTranslation('pharmacy_name', 'ar');

        return (new MailMessage)
            ->subject("⚠️ Low Stock Alert — {$medicineName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Stock for **{$medicineName}** at **{$branchName}** ({$pharmacyName}) is running low.")
            ->line("Current quantity: **{$this->quantity}** units (threshold: {$this->threshold})")
            ->action('View Branch', url("/branches/{$this->branch->branch_slug}"))
            ->line('Please restock as soon as possible.')
            ->salutation('Pharmacy Management System');
    }

    // ──────────────────────────────────────────
    // Database
    // ──────────────────────────────────────────

    /**
     * Stored in the notifications table as JSON.
     * Shaped by NotificationResource::shapeLowStock() for API responses.
     */
    public function toDatabase(object $notifiable): array
    {
        $pharmacy = $this->branch->relationLoaded('pharmacy')
            ? $this->branch->pharmacy
            : $this->branch->load('pharmacy')->pharmacy;

        return [
            'type'          => 'low_stock',
            'medicine_id'   => $this->medicine->id,
            'medicine_slug' => $this->medicine->medicine_slug,
            'medicine_name' => $this->medicine->getTranslations('medicine_name'),
            'branch_id'     => $this->branch->id,
            'branch_slug'   => $this->branch->branch_slug,
            'branch_name'   => $this->branch->getTranslations('branch_name'),
            'pharmacy_id'   => $this->branch->pharmacy_id,
            'pharmacy_name' => $pharmacy->getTranslations('pharmacy_name'),
            'quantity'      => $this->quantity,
            'threshold'     => $this->threshold,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
