<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Branch;
use App\Models\Pharmacy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'pharmacy_id',
        'branch_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    // ──────────────────────────────────────────
    // JWT Implementations
    // ──────────────────────────────────────────

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // ──────────────────────────────────────────
    // Role Helpers
    // ──────────────────────────────────────────

    /**
     * Super admin — no pharmacy_id or branch_id, manages everything.
     * Usage: $user->isSuperAdmin()
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Pharmacy owner — has pharmacy_id, manages own pharmacy + branches.
     * Usage: $user->isPharmacyOwner()
     */
    public function isPharmacyOwner(): bool
    {
        return $this->hasRole('pharmacy-owner');
    }

    /**
     * Branch manager — has pharmacy_id + branch_id, manages one branch.
     * Usage: $user->isBranchManager()
     */
    public function isBranchManager(): bool
    {
        return $this->hasRole('branch-manager');
    }

    /**
     * Client / customer — no pharmacy_id or branch_id.
     * Searches medicines and finds nearby pharmacies.
     * Usage: $user->isClient()
     */
    public function isClient(): bool
    {
        return $this->hasRole('client');
    }

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    /**
     * The pharmacy this user belongs to.
     * Null for clients and super-admins.
     *
     * From migration: users.pharmacy_id FK (nullable) → onDelete('set null')
     *
     * Usage: $user->pharmacy
     */
    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    /**
     * The branch this user is assigned to.
     * Set only for branch-managers.
     * Null for clients, super-admins, and pharmacy-owners.
     *
     * From migration: users.branch_id FK (nullable) → onDelete('set null')
     *
     * Usage: $user->branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // ──────────────────────────────────────────
    // Location Helpers — client only
    // ──────────────────────────────────────────

    /**
     * Get pharmacies near the client's coordinates.
     * Chains Pharmacy::nearby() (Haversine) through branches.
     * Eager loads nearby branches with city for notification payload.
     *
     * Used by: SendNearbyPharmaciesJob → NearbyPharmaciesNotification
     *
     * Usage: $user->nearbyPharmacies(30.0444, 31.2357, 5)->get()
     */
    public function nearbyPharmacies(float $lat, float $lng, int $radiusKm = 5)
    {
        return Pharmacy::nearby($lat, $lng, $radiusKm)
            ->withBranches()
            ->with([
                'branches' => fn(Builder $q) =>
                $q->withCoordinates()
                    ->nearby($lat, $lng, $radiusKm)
                    ->with('city')
            ]);
    }

    /**
     * Get pharmacies near the client that have a specific medicine in stock.
     * Primary feature: "where can I buy X near me?"
     *
     * Used by: SendNearbyPharmaciesJob → NearbyPharmaciesNotification
     *          NotificationController::nearbyPharmacies()
     *
     * Usage: $user->nearbyPharmaciesWithMedicine(30.04, 31.23, medicineId: 5)
     *        $user->nearbyPharmaciesWithMedicine(30.04, 31.23, 5, radiusKm: 10)
     */
    public function nearbyPharmaciesWithMedicine(
        float $lat,
        float $lng,
        int   $medicineId,
        int   $radiusKm = 5
    ) {
        return Pharmacy::nearby($lat, $lng, $radiusKm)
            ->hasMedicineInStock($medicineId)
            ->with([
                'branches' => fn(Builder $q) =>
                $q->withCoordinates()
                    ->nearby($lat, $lng, $radiusKm)
                    ->hasMedicineInStock($medicineId)
                    ->with('city')
            ]);
    }

    // ──────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────

    /**
     * Usage: User::superAdmins()->get();
     */
    public function scopeSuperAdmins(Builder $query): Builder
    {
        return $query->role('super-admin');
    }

    /**
     * Usage: User::pharmacyOwners()->get();
     */
    public function scopePharmacyOwners(Builder $query): Builder
    {
        return $query->role('pharmacy-owner');
    }

    /**
     * Usage: User::branchManagers()->get();
     */
    public function scopeBranchManagers(Builder $query): Builder
    {
        return $query->role('branch-manager');
    }

    /**
     * Usage: User::clients()->get();
     */
    public function scopeClients(Builder $query): Builder
    {
        return $query->role('client');
    }

    /**
     * Users belonging to a specific pharmacy (owners + managers).
     * Used by: CheckLowStockJob to find who to notify.
     *
     * Usage: User::ofPharmacy(1)->get();
     */
    public function scopeOfPharmacy(Builder $query, int $pharmacyId): Builder
    {
        return $query->where('pharmacy_id', $pharmacyId);
    }

    /**
     * Users assigned to a specific branch (managers).
     * Used by: CheckLowStockJob to find who to notify.
     *
     * Usage: User::ofBranch(3)->get();
     */
    public function scopeOfBranch(Builder $query, int $branchId): Builder
    {
        return $query->where('branch_id', $branchId);
    }
}
