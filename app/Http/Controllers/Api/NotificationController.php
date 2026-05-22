<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Jobs\SendNearbyPharmaciesJob;
use App\Models\Medicine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * NotificationController
 *
 * All notification endpoints live here — separate from UserController.
 *
 * Notification types:
 *   low_stock          → sent to branch-manager + pharmacy-owner
 *                        triggered by CheckLowStockJob (scheduled)
 *   nearby_pharmacies  → sent to client
 *                        triggered by SendNearbyPharmaciesJob (dispatched here)
 *
 * All endpoints require auth:sanctum.
 * Each user only sees their own notifications.
 */
class NotificationController extends Controller
{
    /**
     * List notifications for the authenticated user.
     * GET /api/v1/me/notifications
     *
     * Query params:
     *   ?unread=1                   → only unread
     *   ?type=low_stock             → branch-manager / owner alerts only
     *   ?type=nearby_pharmacies     → client location results only
     *   ?per_page=20
     *
     * Response includes unread_count in additional meta.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $query = $request->boolean('unread')
            ? $user->unreadNotifications()
            : $user->notifications();

        if ($request->filled('type')) {
            $request->validate([
                'type' => ['string', 'in:low_stock,nearby_pharmacies'],
            ]);
            $query->where('data->type', $request->input('type'));
        }

        $notifications = $query
            ->latest()
            ->paginate($request->integer('per_page', 100))
            ->withQueryString();

        return NotificationResource::collection($notifications)
            ->additional([
                'meta' => [
                    'unread_count' => $user->unreadNotifications()->count(),
                ],
            ]);
    }

    /**
     * Show one notification — auto-marks as read.
     * GET /api/v1/me/notifications/{id}
     */
    public function show(Request $request, string $id): NotificationResource
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return new NotificationResource($notification);
    }

    /**
     * Mark one notification as read.
     * POST /api/v1/me/notifications/{id}/read
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'message' => __('user.messages.notification_read'),
            'data'    => new NotificationResource($notification),
        ]);
    }

    /**
     * Mark all notifications as read.
     * POST /api/v1/me/notifications/read-all
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return response()->json([
            'message'      => __('user.messages.notifications_read_all'),
            'unread_count' => 0,
        ]);
    }

    /**
     * Delete one notification.
     * DELETE /api/v1/me/notifications/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $request->user()
            ->notifications()
            ->findOrFail($id)
            ->delete();

        return response()->json([
            'message' => __('user.messages.notification_deleted'),
        ]);
    }

    /**
     * Delete all read notifications — inbox cleanup.
     * DELETE /api/v1/me/notifications
     */
    public function destroyAll(Request $request): JsonResponse
    {
        $deleted = $request->user()
            ->notifications()
            ->whereNotNull('read_at')
            ->delete();

        return response()->json([
            'message' => __('user.messages.notifications_cleared'),
            'deleted' => $deleted,
        ]);
    }

    /**
     * Client requests nearby pharmacies.
     * POST /api/v1/me/nearby-pharmacies
     * Auth: client only
     *
     * Dispatches SendNearbyPharmaciesJob — non-blocking, returns 202 immediately.
     * Result arrives as NearbyPharmaciesNotification via GET /me/notifications.
     *
     * Body:
     *   lat       → required float (-90 to 90)
     *   lng       → required float (-180 to 180)
     *   radius    → optional int km, default 5, max 50
     *   medicine  → optional int medicine_id — finds only in-stock pharmacies
     */
    public function nearbyPharmacies(Request $request): JsonResponse
    {
        abort_unless(
            $request->user()->isClient(),
            403,
            __('user.messages.clients_only')
        );

        $v = $request->validate([
            'lat'      => ['required', 'numeric', 'between:-90,90'],
            'lng'      => ['required', 'numeric', 'between:-180,180'],
            'radius'   => ['nullable', 'integer', 'min:1', 'max:50'],
            'medicine' => ['nullable', 'integer', 'exists:medicines,id'],
        ]);

        // Resolve medicine display name for the notification body text
        $medicineName = null;
        if (!empty($v['medicine'])) {
            $medicine     = Medicine::find($v['medicine']);
            $medicineName = $medicine?->getTranslation('medicine_name', app()->getLocale())
                ?? $medicine?->getTranslation('medicine_name', 'en');
        }

        SendNearbyPharmaciesJob::dispatch(
            userId: $request->user()->id,
            lat: (float) $v['lat'],
            lng: (float) $v['lng'],
            radiusKm: (int) ($v['radius'] ?? 5),
            medicineId: $v['medicine'] ?? null,
            medicineName: $medicineName,
        );

        return response()->json(
            ['message' => __('user.messages.nearby_processing')],
            202
        );
    }
}
