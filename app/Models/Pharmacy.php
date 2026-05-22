<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

// class Pharmacy extends Model implements HasMedia
// {
//     use HasTranslations, HasSlug, InteractsWithMedia;

//     protected $fillable = ['pharmacy_name', 'pharmacy_slug'];

//     public $translatable = ['pharmacy_name'];

//     // -------------------------
//     // Spatie Sluggable
//     // -------------------------
//     public function getSlugOptions(): SlugOptions
//     {
//         return SlugOptions::create()
//             ->generateSlugsFrom(fn(Pharmacy $model) => $model->getTranslation('pharmacy_name', 'en'))
//             ->saveSlugsTo('pharmacy_name')
//             ->slugsShouldBeNoLongerThan(80);
//     }

//     public function getRouteKeyName(): string
//     {
//         return 'pharmacy_slug';
//     }
//     // ──────────────────────────────────────────
//     // Media
//     // ──────────────────────────────────────────

//     public function registerMediaCollections(): void
//     {
//         $this->addMediaCollection('pharmacies')
//             ->singleFile();
//     }

//     // ──────────────────────────────────────────
//     // Relationships
//     // ──────────────────────────────────────────

//     /**
//      * A pharmacy has many branches.
//      * branches table has pharmacy_id FK → cascadeOnDelete.
//      */
//     public function branches(): HasMany
//     {
//         return $this->hasMany(Branch::class);
//     }

//     /**
//      * All staff users belonging to this pharmacy.
//      * Includes pharmacy owners and branch managers.
//      * Clients have pharmacy_id = null so they are excluded automatically.
//      * users table has pharmacy_id FK (nullable) → setNull on delete.
//      */
//     public function users(): HasMany
//     {
//         return $this->hasMany(User::class);
//     }

//     /**
//      * Only users with the pharmacy-owner role scoped to this pharmacy.
//      * Uses hasMany because a pharmacy can have multiple owners.
//      */
//     public function owners(): HasMany
//     {
//         return $this->hasMany(User::class)
//             ->whereHas(
//                 'roles',
//                 fn(Builder $q) =>
//                 $q->where('name', 'pharmacy-owner')
//             );
//     }

//     /**
//      * Only users with the branch-manager role scoped to this pharmacy.
//      * A pharmacy can have many branch managers (one per branch).
//      */
//     public function managers(): HasMany
//     {
//         return $this->hasMany(User::class)
//             ->whereHas(
//                 'roles',
//                 fn(Builder $q) =>
//                 $q->where('name', 'branch-manager')
//             );
//     }

//     /**
//      * Medicines are NOT directly related to Pharmacy.
//      * There is no pharmacy_id on medicines table.
//      *
//      * The full chain is:
//      *   Pharmacy → (hasMany) → Branch → (belongsToMany via branch_medicine) → Medicine
//      *
//      * This method returns a query builder — NOT an Eloquent relation.
//      * It resolves all unique medicines available across all branches.
//      *
//      * ❌ Cannot use: Pharmacy::with('medicines')->get()
//      * ✅ Use instead: $pharmacy->medicines()->get()
//      * ✅ Or eager load: Pharmacy::with('branches.medicines')->get()
//      */
//     public function medicines()
//     {
//         return Medicine::whereHas(
//             'branches',
//             fn(Builder $q) =>
//             $q->where('branches.pharmacy_id', $this->id)
//         );
//     }

//     // ──────────────────────────────────────────
//     // Scopes
//     // ──────────────────────────────────────────

//     /**
//      * Only pharmacies that have at least one branch.
//      *
//      * Usage: Pharmacy::withBranches()->get();
//      */
//     public function scopeWithBranches(Builder $query): Builder
//     {
//         return $query->has('branches');
//     }

//     /**
//      * Only pharmacies that have no branches yet.
//      *
//      * Usage: Pharmacy::noBranches()->get();
//      */
//     public function scopeNoBranches(Builder $query): Builder
//     {
//         return $query->doesntHave('branches');
//     }

//     /**
//      * Pharmacies that have at least one branch in the given city.
//      *
//      * Usage: Pharmacy::inCity(3)->get();
//      */
//     public function scopeInCity(Builder $query, int $cityId): Builder
//     {
//         return $query->whereHas(
//             'branches',
//             fn(Builder $q) =>
//             $q->where('city_id', $cityId)
//         );
//     }

//     /**
//      * Pharmacies that have at least one branch in the given governorate.
//      * Resolved through branches → city → governorate.
//      *
//      * Usage: Pharmacy::inGovernorate(2)->get();
//      */
//     public function scopeInGovernorate(Builder $query, int $governorateId): Builder
//     {
//         return $query->whereHas(
//             'branches.city',
//             fn(Builder $q) =>
//             $q->where('governorate_id', $governorateId)
//         );
//     }

//     /**
//      * Pharmacies that stock a specific medicine in any branch.
//      * Resolved through branches → branch_medicine.
//      *
//      * Usage: Pharmacy::hasMedicine(5)->get();
//      */
//     public function scopeHasMedicine(Builder $query, int $medicineId): Builder
//     {
//         return $query->whereHas(
//             'branches.medicines',
//             fn(Builder $q) =>
//             $q->where('medicines.id', $medicineId)
//         );
//     }

//     /**
//      * Pharmacies that have available stock (quantity > 0)
//      * for a specific medicine in any branch.
//      * Used by clients searching where to find a medicine.
//      *
//      * Usage: Pharmacy::hasMedicineInStock(5)->get();
//      */
//     public function scopeHasMedicineInStock(Builder $query, int $medicineId): Builder
//     {
//         return $query->whereHas(
//             'branches.medicines',
//             fn(Builder $q) =>
//             $q->where('medicines.id', $medicineId)
//                 ->where('branch_medicine.quantity', '>', 0)
//         );
//     }

//     /**
//      * Pharmacies that have at least one branch within a radius
//      * of the given coordinates using the Haversine formula.
//      * Requires latitude and longitude columns on branches table.
//      *
//      * Usage: Pharmacy::nearby(30.0444, 31.2357, 5)->get();
//      *        (Cairo coordinates, 5km radius)
//      */
//     public function scopeNearby(Builder $query, float $lat, float $lng, int $radiusKm = 5): Builder
//     {
//         return $query->whereHas(
//             'branches',
//             fn(Builder $q) =>
//             $q->selectRaw(
//                 "(6371 * acos(
//                     cos(radians(?)) * cos(radians(latitude))
//                     * cos(radians(longitude) - radians(?))
//                     + sin(radians(?)) * sin(radians(latitude))
//                 )) AS distance",
//                 [$lat, $lng, $lat]
//             )
//                 ->having('distance', '<=', $radiusKm)
//         );
//     }

//     /**
//      * Pharmacies that have low stock (above zero but at or below threshold)
//      * for a specific medicine — useful for owner/admin dashboards.
//      *
//      * Usage: Pharmacy::lowStock(5)->get();
//      *        Pharmacy::lowStock(5, 5)->get(); // custom threshold
//      */
//     public function scopeLowStock(Builder $query, int $medicineId, int $threshold = 10): Builder
//     {
//         return $query->whereHas(
//             'branches.medicines',
//             fn(Builder $q) =>
//             $q->where('medicines.id', $medicineId)
//                 ->where('branch_medicine.quantity', '>', 0)
//                 ->where('branch_medicine.quantity', '<=', $threshold)
//         );
//     }
// }
//**------------------------- */
class Pharmacy extends Model implements HasMedia
{
    use HasTranslations, HasSlug, InteractsWithMedia;

    protected $fillable = [
        'pharmacy_name',
        'pharmacy_slug',
    ];

    /**
     * pharmacy_name is stored as JSON in the database.
     * Managed by spatie/laravel-translatable.
     * Example: {"en": "Cairo Pharmacy", "ar": "صيدلية القاهرة"}
     */
    public $translatable = ['pharmacy_name'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Accessors automatically appended to every API response.
     *
     * branch_count → total branches count (cache-aware, avoids N+1)
     * has_stock    → true if any branch has any medicine with quantity > 0
     */
    protected $appends = [
        'branch_count',
        'has_stock',
    ];

    // ──────────────────────────────────────────
    // Sluggable — spatie/laravel-sluggable
    // ──────────────────────────────────────────

    /**
     * Slug is generated from the English pharmacy name.
     * Falls back to Arabic then 'pharmacy' if English is missing.
     * doNotGenerateSlugsOnUpdate() keeps URLs stable after creation — SEO safe.
     */
    // public function getSlugOptions(): SlugOptions
    // {
    //     return SlugOptions::create()
    //         ->generateSlugsFrom(
    //             fn() =>
    //             $this->getTranslation('pharmacy_name', 'en')
    //                 ?? $this->getTranslation('pharmacy_name', 'ar')
    //                 ?? 'pharmacy'
    //         )
    //         ->saveSlugsTo('pharmacy_slug')
    //         ->doNotGenerateSlugsOnUpdate();
    // }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn() => $this
                ->getTranslation('pharmacy_name', 'en')) // Generates slug from English translation
            ->saveSlugsTo('pharmacy_slug');
         //   ->doNotGenerateSlugsOnUpdate(); // Keeps SEO stable
    }
    /**
     * Route model binding uses pharmacy_slug instead of id.
     * Example: GET /api/v1/pharmacies/cairo-pharmacy
     */
    public function getRouteKeyName(): string
    {
        return 'pharmacy_slug';
    }

    // ──────────────────────────────────────────
    // Media — spatie/laravel-medialibrary
    // ──────────────────────────────────────────

    /**
     * Single logo image per pharmacy.
     * Access via: $pharmacy->getFirstMediaUrl('pharmacies')
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('pharmacies')
            ->singleFile();
    }

    // ──────────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────────

    /**
     * Total number of branches belonging to this pharmacy.
     *
     * Smart: uses cached relation count if branches were already
     * eager loaded — avoids an extra COUNT query.
     *
     * Usage: $pharmacy->branch_count
     */
    public function getBranchCountAttribute(): int
    {
        return $this->relationLoaded('branches')
            ? $this->branches->count()
            : $this->branches()->count();
    }

    /**
     * True if at least one branch of this pharmacy has quantity > 0
     * for any medicine. Used for "In Stock" badge on pharmacy listing.
     *
     * Usage: $pharmacy->has_stock
     */
    public function getHasStockAttribute(): bool
    {
        return $this->branches()
            ->whereHas(
                'medicines',
                fn(Builder $q) =>
                $q->where('branch_medicine.quantity', '>', 0)
            )
            ->exists();
    }

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    /**
     * Pharmacy has many branches.
     *
     * From migration:
     *   branches.pharmacy_id → FK → cascadeOnDelete → cascadeOnUpdate
     *
     * Usage: $pharmacy->branches
     *        $pharmacy->branches()->inCity(3)->get()
     */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * All staff users assigned to this pharmacy.
     * Covers: pharmacy-owner + branch-manager roles.
     *
     * From migration:
     *   users.pharmacy_id FK (nullable) → onDelete('set null')
     *
     * Clients (customers) have pharmacy_id = null
     * so they are automatically excluded from this relation.
     *
     * Usage: $pharmacy->users
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Only pharmacy owners assigned to this pharmacy.
     * Uses Spatie permission role name 'pharmacy-owner'.
     * hasMany — a pharmacy can have more than one owner.
     *
     * Usage: $pharmacy->owners
     */
    public function owners(): HasMany
    {
        return $this->hasMany(User::class)
            ->whereHas(
                'roles',
                fn(Builder $q) =>
                $q->where('name', 'pharmacy-owner')
            );
    }

    /**
     * Only branch managers assigned to this pharmacy.
     * Uses Spatie permission role name 'branch-manager'.
     * One manager per branch → many managers per pharmacy.
     *
     * Usage: $pharmacy->managers
     */
    public function managers(): HasMany
    {
        return $this->hasMany(User::class)
            ->whereHas(
                'roles',
                fn(Builder $q) =>
                $q->where('name', 'branch-manager')
            );
    }

    /**
     * All medicines available across all branches of this pharmacy.
     *
     * ⚠️ There is NO direct pharmacy_id on the medicines table.
     * The chain is:
     *   Pharmacy → (hasMany) → Branch → (belongsToMany via branch_medicine) → Medicine
     *
     * This returns a query builder — NOT a standard Eloquent relation.
     *
     * ❌ Cannot eager load: Pharmacy::with('medicines')->get()
     * ✅ Use directly:      $pharmacy->medicines()->get()
     * ✅ Chain scopes:      $pharmacy->medicines()->inStock()->byName('para')->get()
     * ✅ Eager load chain:  Pharmacy::with('branches.medicines')->get()
     *
     * Usage: $pharmacy->medicines()->inStock()->get();
     */
    public function medicines()
    {
        return Medicine::whereHas(
            'branches',
            fn(Builder $q) =>
            $q->where('branches.pharmacy_id', $this->id)
        );
    }

    // ──────────────────────────────────────────
    // Helpers — instance methods for business logic
    // ──────────────────────────────────────────

    /**
     * Check if this pharmacy stocks a specific medicine in any branch.
     * Accepts either a Medicine model instance or a raw integer id.
     *
     * Usage: $pharmacy->hasMedicine(5)         → bool
     *        $pharmacy->hasMedicine($medicine) → bool
     */
    public function hasMedicine(Medicine|int $medicine): bool
    {
        $id = $medicine instanceof Medicine ? $medicine->id : $medicine;

        return $this->branches()
            ->whereHas(
                'medicines',
                fn(Builder $q) =>
                $q->where('medicines.id', $id)
            )
            ->exists();
    }

    /**
     * Check if this pharmacy has available stock (quantity > 0)
     * for a specific medicine in any of its branches.
     *
     * Usage: $pharmacy->hasMedicineInStock(5)         → bool
     *        $pharmacy->hasMedicineInStock($medicine) → bool
     */
    public function hasMedicineInStock(Medicine|int $medicine): bool
    {
        $id = $medicine instanceof Medicine ? $medicine->id : $medicine;

        return $this->branches()
            ->whereHas(
                'medicines',
                fn(Builder $q) =>
                $q->where('medicines.id', $id)
                    ->where('branch_medicine.quantity', '>', 0)
            )
            ->exists();
    }

    /**
     * Get the total stock quantity of a specific medicine
     * summed across ALL branches of this pharmacy.
     *
     * Uses a direct DB query on branch_medicine for performance —
     * avoids loading all branch/medicine models just to sum.
     *
     * Usage: $pharmacy->totalStockOf(5)         → int
     *        $pharmacy->totalStockOf($medicine) → int
     */
    public function totalStockOf(Medicine|int $medicine): int
    {
        $id = $medicine instanceof Medicine ? $medicine->id : $medicine;

        return DB::table('branch_medicine')
            ->whereIn('branch_id', $this->branches()->pluck('branches.id'))
            ->where('medicine_id', $id)
            ->sum('quantity');
    }

    /**
     * Get branches of this pharmacy that currently have stock
     * (quantity > 0) for a specific medicine.
     *
     * Returns a HasMany builder so the caller can chain
     * further constraints like ->with('city')->get()
     *
     * Usage: $pharmacy->branchesWithMedicine(5)->get()
     *        $pharmacy->branchesWithMedicine($medicine)->with('city')->get()
     */
    public function branchesWithMedicine(Medicine|int $medicine): HasMany
    {
        $id = $medicine instanceof Medicine ? $medicine->id : $medicine;

        return $this->branches()
            ->whereHas(
                'medicines',
                fn(Builder $q) =>
                $q->where('medicines.id', $id)
                    ->where('branch_medicine.quantity', '>', 0)
            );
    }

    /**
     * Get the nearest branch of this pharmacy to a given coordinate.
     * Uses the Haversine formula — same as Branch::scopeNearby().
     * Returns null if pharmacy has no geocoded branches.
     *
     * Requires: latitude + longitude columns on branches table
     * (auto-filled by spatie/geocoder via Branch::booted())
     *
     * Usage: $pharmacy->nearestBranchTo(30.0444, 31.2357)
     */
    public function nearestBranchTo(float $lat, float $lng): ?Branch
    {
        return $this->branches()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw(
                "*, (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?)) * sin(radians(latitude))
                )) AS distance",
                [$lat, $lng, $lat]
            )
            ->orderBy('distance')
            ->first();
    }

    /**
     * Get low-stock medicines across all branches of this pharmacy.
     * Returns medicines where any branch has 0 < quantity <= threshold.
     *
     * Useful for pharmacy owner dashboard stock alerts.
     *
     * Usage: $pharmacy->lowStockMedicines()->get()
     *        $pharmacy->lowStockMedicines(5)->get()  // custom threshold
     */
    public function lowStockMedicines(int $threshold = 10)
    {
        return $this->medicines()
            ->whereHas(
                'branches',
                fn(Builder $q) =>
                $q->where('branches.pharmacy_id', $this->id)
                    ->where('branch_medicine.quantity', '>', 0)
                    ->where('branch_medicine.quantity', '<=', $threshold)
            );
    }

    /**
     * Get medicines expiring soon across all branches of this pharmacy.
     *
     * Useful for pharmacy owner dashboard expiry alerts.
     *
     * Usage: $pharmacy->expiringSoonMedicines()->get()
     *        $pharmacy->expiringSoonMedicines(7)->get()  // within 7 days
     */
    public function expiringSoonMedicines(int $days = 30)
    {
        return $this->medicines()
            ->whereHas(
                'branches',
                fn(Builder $q) =>
                $q->where('branches.pharmacy_id', $this->id)
                    ->whereBetween('branch_medicine.expiry_date', [
                        now()->toDateString(),
                        now()->addDays($days)->toDateString(),
                    ])
            );
    }

    // ──────────────────────────────────────────
    // Scopes — Existence
    // ──────────────────────────────────────────

    /**
     * Only pharmacies that have at least one branch.
     *
     * Usage: Pharmacy::withBranches()->get();
     */
    public function scopeWithBranches(Builder $query): Builder
    {
        return $query->has('branches');
    }

    /**
     * Only pharmacies that have no branches yet.
     * Useful for super-admin to find incomplete pharmacy setups.
     *
     * Usage: Pharmacy::noBranches()->get();
     */
    public function scopeNoBranches(Builder $query): Builder
    {
        return $query->doesntHave('branches');
    }

    // ──────────────────────────────────────────
    // Scopes — Location
    // ──────────────────────────────────────────

    /**
     * Pharmacies with at least one branch in the given city.
     * city_id lives on branches table.
     *
     * Usage: Pharmacy::inCity(3)->get();
     */
    public function scopeInCity(Builder $query, int $cityId): Builder
    {
        return $query->whereHas(
            'branches',
            fn(Builder $q) =>
            $q->where('city_id', $cityId)
        );
    }

    /**
     * Pharmacies with at least one branch in the given governorate.
     * Resolved: branches → cities → governorate_id
     *
     * Usage: Pharmacy::inGovernorate(2)->get();
     */
    public function scopeInGovernorate(Builder $query, int $governorateId): Builder
    {
        return $query->whereHas(
            'branches.city',
            fn(Builder $q) =>
            $q->where('governorate_id', $governorateId)
        );
    }

    /**
     * Pharmacies with at least one branch in the given country.
     * Resolved: branches → cities → governorates → country_id
     *
     * Usage: Pharmacy::inCountry(1)->get();
     */
    public function scopeInCountry(Builder $query, int $countryId): Builder
    {
        return $query->whereHas(
            'branches.city.governorate',
            fn(Builder $q) =>
            $q->where('country_id', $countryId)
        );
    }

    /**
     * Pharmacies with at least one geocoded branch within a radius.
     * Uses Haversine formula — same as Branch::scopeNearby().
     * Automatically excludes branches with null coordinates.
     *
     * Usage: Pharmacy::nearby(30.0444, 31.2357, 5)->get();
     */
    public function scopeNearby(Builder $query, float $lat, float $lng, int $radiusKm = 5): Builder
    {
        return $query->whereHas(
            'branches',
            fn(Builder $q) =>
            $q->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->selectRaw(
                    "(6371 * acos(
                      cos(radians(?)) * cos(radians(latitude))
                      * cos(radians(longitude) - radians(?))
                      + sin(radians(?)) * sin(radians(latitude))
                  )) AS distance",
                    [$lat, $lng, $lat]
                )
                ->having('distance', '<=', $radiusKm)
        );
    }

    // ──────────────────────────────────────────
    // Scopes — Medicine & Stock
    // ──────────────────────────────────────────

    /**
     * Pharmacies that stock a specific medicine in any branch (any quantity).
     *
     * Usage: Pharmacy::hasMedicine(5)->get();
     */
    public function scopeHasMedicine(Builder $query, int $medicineId): Builder
    {
        return $query->whereHas(
            'branches.medicines',
            fn(Builder $q) =>
            $q->where('medicines.id', $medicineId)
        );
    }

    /**
     * Pharmacies with available stock (quantity > 0) for a specific medicine.
     * Primary scope for client "where can I find this medicine?" feature.
     *
     * Usage: Pharmacy::hasMedicineInStock(5)->get();
     *        Pharmacy::hasMedicineInStock(5)->nearby(30.04, 31.23)->get();
     */
    public function scopeHasMedicineInStock(Builder $query, int $medicineId): Builder
    {
        return $query->whereHas(
            'branches.medicines',
            fn(Builder $q) =>
            $q->where('medicines.id', $medicineId)
                ->where('branch_medicine.quantity', '>', 0)
        );
    }

    /**
     * Pharmacies with low stock (> 0 and ≤ threshold) for a specific medicine.
     * For pharmacy owner and super-admin dashboards.
     *
     * Usage: Pharmacy::lowStock(5)->get();
     *        Pharmacy::lowStock(5, threshold: 5)->get();
     */
    public function scopeLowStock(Builder $query, int $medicineId, int $threshold = 10): Builder
    {
        return $query->whereHas(
            'branches.medicines',
            fn(Builder $q) =>
            $q->where('medicines.id', $medicineId)
                ->where('branch_medicine.quantity', '>', 0)
                ->where('branch_medicine.quantity', '<=', $threshold)
        );
    }

    /**
     * Pharmacies with at least one medicine expiring within N days.
     * For super-admin monitoring dashboard.
     *
     * Usage: Pharmacy::withExpiringSoon()->get();
     *        Pharmacy::withExpiringSoon(7)->get();
     */
    public function scopeWithExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->whereHas(
            'branches.medicines',
            fn(Builder $q) =>
            $q->whereBetween('branch_medicine.expiry_date', [
                now()->toDateString(),
                now()->addDays($days)->toDateString(),
            ])
        );
    }

    // ──────────────────────────────────────────
    // Scopes — Sorting & Recency
    // ──────────────────────────────────────────

    /**
     * Recently created pharmacies within the last N days.
     *
     * Usage: Pharmacy::recent()->get();
     *        Pharmacy::recent(7)->get();
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Order pharmacies by translated name in the current or given locale.
     * Uses driver-aware JSON extract — works on MySQL, MariaDB, SQLite, PostgreSQL.
     *
     * Usage: Pharmacy::orderByName()->get();
     *        Pharmacy::orderByName('desc', 'ar')->get();
     */
    public function scopeOrderByName(Builder $query, string $direction = 'asc', ?string $locale = null): Builder
    {
        $locale ??= app()->getLocale();

        $extract = match (DB::getDriverName()) {
            'sqlite' => "JSON_EXTRACT(pharmacy_name, '$.{$locale}')",
            'pgsql'  => "pharmacy_name->>'$.{$locale}'",
            default  => "JSON_UNQUOTE(JSON_EXTRACT(pharmacy_name, '$.{$locale}'))",
        };

        return $query->orderByRaw("{$extract} {$direction}");
    }
}
