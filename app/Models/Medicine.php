<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Medicine extends Model implements HasMedia
{
    use HasTranslations, HasSlug, InteractsWithMedia;

    protected $fillable = [
        'category_id',
        'medicine_name',
        'medicine_description',
        'medicine_usage',
        'medicine_side_effects',
        'medicine_slug',
        'medicine_price',
    ];

    public array $translatable = [
        'medicine_name',
        'medicine_description',
        'medicine_usage',
        'medicine_side_effects',
    ];

    protected $casts = [
        'medicine_price' => 'decimal:2',
    ];

    /**
     * Appended accessors included in all API responses.
     * total_stock → sum of quantity across all branches.
     * No average_price — price lives on the medicine itself only.
     */
    protected $appends = [
        'total_stock',
    ];

    // ──────────────────────────────────────────
    // Sluggable
    // ──────────────────────────────────────────

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(
                fn() =>
                $this->getTranslation('medicine_name', 'en')
                    ?? $this->getTranslation('medicine_name', 'ar')
                    ?? 'medicine'
            )
            ->saveSlugsTo('medicine_slug')
            ->doNotGenerateSlugsOnUpdate(); // keeps SEO stable after creation
    }

    public function getRouteKeyName(): string
    {
        return 'medicine_slug';
    }

    // ──────────────────────────────────────────
    // Media
    // ──────────────────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('medicines')
            ->singleFile();
    }

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    /**
     * Medicine belongs to one category.
     * categories table — category_id FK on medicines.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Medicine belongs to many branches via branch_medicine pivot.
     *
     * Pivot columns:
     *   quantity    → stock count for this medicine in this branch.
     *   price       → branch-level price override (can differ per branch).
     *   expiry_date → expiry date of the stock batch in this branch.
     *
     * NOTE: ensure your branch_medicine migration has:
     *   $table->decimal('price', 8, 2)->nullable();
     *   $table->date('expiry_date')->nullable();
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_medicine')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Pharmacies that stock this medicine.
     * No direct FK — resolved through branches.
     *
     * Chain: Medicine → branch_medicine → Branch → Pharmacy
     *
     * Returns a query builder, NOT an Eloquent relation.
     * ❌ Cannot use: Medicine::with('pharmacies')->get()
     * ✅ Use instead: $medicine->pharmacies()->get()
     */
    public function pharmacies()
    {
        return Pharmacy::whereHas(
            'branches.medicines',
            fn(Builder $q) =>
            $q->where('medicines.id', $this->id)
        );
    }

    // ──────────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────────

    /**
     * Total stock quantity across ALL branches.
     * Sums branch_medicine.quantity for this medicine.
     *
     * Usage: $medicine->total_stock
     */
    public function getTotalStockAttribute(): int
    {
        return $this->branches()->sum('branch_medicine.quantity');
    }

    // ──────────────────────────────────────────
    // JSON extract helper — driver aware
    // ──────────────────────────────────────────

    /**
     * Builds a driver-aware JSON extract expression.
     * Used internally by scopeByName and scopeOrderByName.
     */
    private static function jsonExtract(string $column, string $locale): string
    {
        return match (DB::getDriverName()) {
            'sqlite' => "JSON_EXTRACT({$column}, '$.{$locale}')",
            'pgsql'  => "{$column}->>'$.{$locale}'",
            default  => "JSON_UNQUOTE(JSON_EXTRACT({$column}, '$.{$locale}'))", // MySQL / MariaDB
        };
    }

    // ──────────────────────────────────────────
    // Scopes — Search & Filter
    // ──────────────────────────────────────────

    /**
     * Filter by partial medicine name in current or given locale.
     *
     * Usage: Medicine::byName('para')->get();
     *        Medicine::byName('باراسيتامول', 'ar')->get();
     */
    public function scopeByName(Builder $query, string $name, ?string $locale = null): Builder
    {
        $locale ??= app()->getLocale();
        $extract = self::jsonExtract('medicine_name', $locale);

        return $query->whereRaw("{$extract} LIKE ?", ["%{$name}%"]);
    }

    /**
     * Filter by exact slug.
     *
     * Usage: Medicine::bySlug('paracetamol')->first();
     */
    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('medicine_slug', $slug);
    }

    /**
     * Filter by category id.
     *
     * Usage: Medicine::byCategory(2)->get();
     */
    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Filter by category slug.
     *
     * Usage: Medicine::byCategorySlug('antibiotics')->get();
     */
    public function scopeByCategorySlug(Builder $query, string $slug): Builder
    {
        return $query->whereHas(
            'category',
            fn(Builder $q) =>
            $q->where('category_slug', $slug)
        );
    }

    // ──────────────────────────────────────────
    // Scopes — Price
    // ──────────────────────────────────────────

    /**
     * Medicines with a base price within a given range.
     * Uses medicine_price column on medicines table.
     *
     * Usage: Medicine::priceRange(10, 50)->get();
     */
    public function scopePriceRange(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('medicine_price', [$min, $max]);
    }

    /**
     * Medicines with a base price at or below a maximum.
     *
     * Usage: Medicine::maxPrice(30)->get();
     */
    public function scopeMaxPrice(Builder $query, float $max): Builder
    {
        return $query->where('medicine_price', '<=', $max);
    }

    /**
     * Medicines with a base price at or above a minimum.
     *
     * Usage: Medicine::minPrice(10)->get();
     */
    public function scopeMinPrice(Builder $query, float $min): Builder
    {
        return $query->where('medicine_price', '>=', $min);
    }

    /**
     * Medicines available at or below a price in a specific branch.
     * Checks branch-level price override on the pivot.
     * If no branchId is given, checks across all branches.
     *
     * Usage: Medicine::availableAtPrice(25)->get();
     *        Medicine::availableAtPrice(25, branchId: 3)->get();
     */
    public function scopeAvailableAtPrice(Builder $query, float $max, ?int $branchId = null): Builder
    {
        return $query->whereHas('branches', function (Builder $q) use ($max, $branchId) {
            $q->where('branch_medicine.price', '<=', $max);

            if ($branchId) {
                $q->where('branches.id', $branchId);
            }
        });
    }

    /**
     * Order medicines by base price.
     *
     * Usage: Medicine::orderByPrice()->get();
     *        Medicine::orderByPrice('desc')->get();
     */
    public function scopeOrderByPrice(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('medicine_price', $direction);
    }

    // ──────────────────────────────────────────
    // Scopes — Location
    // ──────────────────────────────────────────

    /**
     * Medicines stocked in a specific branch.
     *
     * Usage: Medicine::inBranch(3)->get();
     */
    public function scopeInBranch(Builder $query, int $branchId): Builder
    {
        return $query->whereHas(
            'branches',
            fn(Builder $q) =>
            $q->where('branches.id', $branchId)
        );
    }

    /**
     * Medicines available in any branch of a specific pharmacy.
     *
     * Usage: Medicine::inPharmacy(1)->get();
     */
    public function scopeInPharmacy(Builder $query, int $pharmacyId): Builder
    {
        return $query->whereHas(
            'branches',
            fn(Builder $q) =>
            $q->where('branches.pharmacy_id', $pharmacyId)
        );
    }

    /**
     * Medicines available in any branch within a specific city.
     * Used by clients searching medicines near their city.
     *
     * Usage: Medicine::inCity(5)->get();
     */
    public function scopeInCity(Builder $query, int $cityId): Builder
    {
        return $query->whereHas(
            'branches',
            fn(Builder $q) =>
            $q->where('branches.city_id', $cityId)
        );
    }

    /**
     * Medicines available within a radius using Haversine formula.
     * Requires latitude and longitude decimal columns on branches table.
     *
     * Usage: Medicine::nearby(30.0444, 31.2357, 5)->get();
     *        (Cairo coordinates, 5km radius)
     */
    public function scopeNearby(Builder $query, float $lat, float $lng, int $radiusKm = 5): Builder
    {
        return $query->whereHas(
            'branches',
            fn(Builder $q) =>
            $q->selectRaw(
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
    // Scopes — Stock
    // ──────────────────────────────────────────

    /**
     * Medicines with available stock (quantity > 0) in any branch.
     *
     * Usage: Medicine::inStock()->get();
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->whereHas(
            'branches',
            fn(Builder $q) =>
            $q->where('branch_medicine.quantity', '>', 0)
        );
    }

    /**
     * Medicines with zero stock across all branches.
     *
     * Usage: Medicine::outOfStock()->get();
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->whereDoesntHave(
            'branches',
            fn(Builder $q) =>
            $q->where('branch_medicine.quantity', '>', 0)
        );
    }

    /**
     * Medicines with stock above zero but at or below a threshold.
     * Used to trigger low-stock alerts for branch managers.
     *
     * Usage: Medicine::lowStock()->get();
     *        Medicine::lowStock(5)->get();
     */
    public function scopeLowStock(Builder $query, int $threshold = 10): Builder
    {
        return $query->whereHas(
            'branches',
            fn(Builder $q) =>
            $q->where('branch_medicine.quantity', '>', 0)
                ->where('branch_medicine.quantity', '<=', $threshold)
        );
    }

    // ──────────────────────────────────────────
    // Scopes — Expiry
    // ──────────────────────────────────────────

    /**
     * Medicines with expiry date on or before a given date.
     *
     * Usage: Medicine::expiringBefore('2025-12-31')->get();
     */
    public function scopeExpiringBefore(Builder $query, string $date): Builder
    {
        return $query->whereHas(
            'branches',
            fn(Builder $q) =>
            $q->where('branch_medicine.expiry_date', '<=', $date)
        );
    }

    /**
     * Medicines expiring within the next N days.
     * Used for expiry alerts in branch manager dashboard.
     *
     * Usage: Medicine::expiringSoon()->get();       // next 30 days
     *        Medicine::expiringSoon(7)->get();      // next 7 days
     */
    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->whereHas(
            'branches',
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
     * Recently added medicines within the last N days.
     *
     * Usage: Medicine::recent()->get();
     *        Medicine::recent(7)->get();
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Order medicines by translated name in current or given locale.
     *
     * Usage: Medicine::orderByName()->get();
     *        Medicine::orderByName('desc', 'ar')->get();
     */
    public function scopeOrderByName(Builder $query, string $direction = 'asc', ?string $locale = null): Builder
    {
        $locale ??= app()->getLocale();
        $extract = self::jsonExtract('medicine_name', $locale);

        return $query->orderByRaw("{$extract} {$direction}");
    }
}
