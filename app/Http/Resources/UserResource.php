<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $authUser = $request->user();
        $isSelf   = $authUser && (int) $authUser->id === (int) $this->id;
        $isAdmin  = $authUser?->isSuperAdmin();

        return [
            // ── Identity ──────────────────────────────────────
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,

            // ── Role ──────────────────────────────────────────
            'role'              => $this->getRoleNames()->first(),
            'is_super_admin'    => $this->isSuperAdmin(),
            'is_pharmacy_owner' => $this->isPharmacyOwner(),
            'is_branch_manager' => $this->isBranchManager(),
            'is_client'         => $this->isClient(),

            // ── Verification ──────────────────────────────────
            'email_verified_at' => $this->email_verified_at?->toDateTimeString(),

            // ── Sensitive FKs (own profile or admin only) ─────
            'pharmacy_id' => $this->when($isSelf || $isAdmin, $this->pharmacy_id),
            'branch_id'   => $this->when($isSelf || $isAdmin, $this->branch_id),

            // ── Unread notifications count ────────────────────
            // Included only for own profile or admin viewing
            // Mobile app uses this for the bell badge counter
            'unread_notifications_count' => $this->when(
                $isSelf || $isAdmin,
                fn() => $this->unreadNotifications()->count()
            ),

            // ── Timestamps ────────────────────────────────────
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),

            // ── Relationships (only when ?include= is passed) ──
            'pharmacy' => new PharmacyResource($this->whenLoaded('pharmacy')),
            'branch'   => new BranchResource($this->whenLoaded('branch')),
            // 🌟 ── الإضافة المطلوبة لـ Angular (Spatie) ── 🌟
            // نرسل مصفوفة الأدوار كاملة
            'roles' => $this->getRoleNames(),
            // نرسل مصفوفة الأذونات (Permissions) فقط إذا كان المستخدم يطلب حسابه الشخصي أو كان أدمن
            'permissions' => $this->when($isSelf || $isAdmin, function () {
                return $this->getAllPermissions()->pluck('name');
            }),
        ];
    }
}
