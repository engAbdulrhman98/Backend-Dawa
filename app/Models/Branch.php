<?php

namespace App\Models;

use App\Models\City;
use App\Models\Governorate;
use GoogleMaps\Facade\GoogleMapsFacade as GoogleMaps;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Geocoder\Facades\Geocoder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Branch extends Model implements HasMedia
{
    use HasTranslations, HasSlug, InteractsWithMedia;

    protected $fillable = [
        'pharmacy_id',
        'city_id',
        'branch_name',
        'branch_address',
        'branch_phone',
        'branch_slug',
        'latitude',
        'longitude',
    ];

    /**
     * Translatable JSON fields — managed by spatie/laravel-translatable.
     * Stored in DB as: {"en": "...", "ar": "..."}
     *
     * branch_phone is translatable because phone formats can differ
     * per locale (e.g. Arabic digits vs Western digits).
     */
    public array $translatable = [
        'branch_name',
        'branch_address',
        'branch_phone',
    ];

    protected $casts = [
        'latitude'   => 'decimal:8',
        'longitude'  => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Appended accessors automatically included in every API response.
     *
     * map_url       → Google Maps deep link for mobile navigation (no DB column needed)
     * map_embed_url → Google Maps embed URL for webview display
     * directions_url→ Google Maps directions URL from any origin
     * total_stock   → sum of all medicine quantities in this branch
     * has_low_stock → bool — triggers dashboard alert badge
     */
    protected $appends = [
        'map_url',
        'map_embed_url',
        'directions_url',
        'total_stock',
        'has_low_stock',
    ];

    // ──────────────────────────────────────────
    // Sluggable — spatie/laravel-sluggable
    // ──────────────────────────────────────────

    /**
     * Slug generated from English branch name.
     * Falls back to Arabic then 'branch' if English is missing.
     * doNotGenerateSlugsOnUpdate() keeps URLs stable after creation.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(
                fn() =>
                $this->getTranslation('branch_name', 'en')
                    ?? $this->getTranslation('branch_name', 'ar')
                    ?? 'branch'
            )
            ->saveSlugsTo('branch_slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Route model binding uses branch_slug instead of id.
     * Example: GET /api/v1/branches/cairo-downtown
     */
    public function getRouteKeyName(): string
    {
        return 'branch_slug';
    }

    // ──────────────────────────────────────────
    // Media — spatie/laravel-medialibrary
    // ──────────────────────────────────────────

    /**
     * Single image per branch (storefront photo).
     * Access via: $branch->getFirstMediaUrl('branches')
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('branches')
            ->singleFile();
    }

    // ──────────────────────────────────────────
    // Auto-Geocoding — spatie/geocoder
    // ──────────────────────────────────────────

    /**
     * Automatically fill latitude and longitude from the branch address
     * when saving a branch that has no coordinates yet.
     *
     * Triggered on: Branch::create() and $branch->save()
     *
     * Requires in .env:
     *   GOOGLE_MAPS_GEOCODING_API_KEY=your_key_here
     *
     * ✅ You do NOT need to store a Google Maps link in the DB.
     *    All map links (map_url, map_embed_url, directions_url) are
     *    generated on the fly from latitude + longitude as accessors.
     */
    protected static function booted(): void
    {
        static::saving(function (Branch $branch) {
            $missingCoordinates = empty($branch->latitude) || empty($branch->longitude);

            $address = $branch->getTranslation('branch_address', 'en')
                ?? $branch->getTranslation('branch_address', 'ar');

            if ($missingCoordinates && $address) {
                try {
                    $result = Geocoder::getCoordinatesForAddress($address);

                    if (isset($result['lat'], $result['lng']) && $result['lat'] !== 0) {
                        $branch->latitude  = $result['lat'];
                        $branch->longitude = $result['lng'];
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Geocoding failed for branch address: {$address}. Error: " . $e->getMessage());
                    // Fallback to default Cairo coordinates if none exist, or keep them null
                    $branch->latitude  = $branch->latitude ?? 30.0444;
                    $branch->longitude = $branch->longitude ?? 31.2357;
                }
            }
        });
    }

    /**
     * Manually re-geocode the branch address and update coordinates.
     * Call this when the address is updated and coordinates need refreshing.
     *
     * Usage: $branch->refreshCoordinates()->save();
     */
    public function refreshCoordinates(): static
    {
        $address = $this->getTranslation('branch_address', 'en')
            ?? $this->getTranslation('branch_address', 'ar');

        if (!$address) {
            return $this;
        }

        try {
            $result = Geocoder::getCoordinatesForAddress($address);

            if (isset($result['lat'], $result['lng']) && $result['lat'] !== 0) {
                $this->latitude  = $result['lat'];
                $this->longitude = $result['lng'];
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Geocoding failed during coordinate refresh for address: {$address}. Error: " . $e->getMessage());
        }

        return $this;
    }

    // ──────────────────────────────────────────
    // Google Maps API — alexpechkarev/google-maps
    // ──────────────────────────────────────────

    /**
     * Get full Google Place details for this branch.
     * Returns ratings, opening hours, photos, reviews from Google Places.
     *
     * Requires a Google Place ID — recommended to store as a DB column:
     *   $table->string('google_place_id')->nullable();
     *
     * Usage: $branch->getGooglePlaceDetails('ChIJ...');
     */
    public function getGooglePlaceDetails(string $placeId): array|null
    {
        $response = GoogleMaps::load('placedetails')
            ->setParam(['placeid' => $placeId])
            ->get();

        $data = json_decode($response, true);

        return $data['result'] ?? null;
    }

    /**
     * Find places of a given type near this branch using Google Places.
     * Useful for showing nearby hospitals, clinics, or pharmacies.
     *
     * Usage: $branch->getNearbyPlaces('hospital', 2000);
     *        $branch->getNearbyPlaces('pharmacy', 1000);
     */
    public function getNearbyPlaces(string $type = 'pharmacy', int $radius = 1000): array
    {
        $response = GoogleMaps::load('placesnearby')
            ->setParam([
                'location' => "{$this->latitude},{$this->longitude}",
                'radius'   => $radius,
                'type'     => $type,
            ])
            ->get();

        $data = json_decode($response, true);

        return $data['results'] ?? [];
    }

    /**
     * Get driving route from a user's location to this branch.
     * Uses the Routes API (replaces deprecated Distance Matrix API).
     * Returns distance, duration, and polyline for the mobile app map.
     *
     * Usage: $branch->getRouteFrom(30.0444, 31.2357);
     */
    public function getRouteFrom(float $originLat, float $originLng): array|null
    {
        $response = GoogleMaps::load('routes')
            ->setParam([
                'origin' => [
                    'location' => [
                        'latLng' => [
                            'latitude'  => $originLat,
                            'longitude' => $originLng,
                        ],
                    ],
                ],
                'destination' => [
                    'location' => [
                        'latLng' => [
                            'latitude'  => (float) $this->latitude,
                            'longitude' => (float) $this->longitude,
                        ],
                    ],
                ],
                'travelMode' => 'DRIVE',
            ])
            ->get();

        $data = json_decode($response, true);

        return $data['routes'][0] ?? null;
    }

    // ──────────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────────

    /**
     * Google Maps deep link — opens the Google Maps app directly on mobile.
     * Best for "Open in Maps" buttons in the mobile app.
     *
     * ✅ Generated from latitude + longitude — no DB column needed.
     * Returns null if coordinates are not set yet.
     *
     * Usage: $branch->map_url
     */
    public function getMapUrlAttribute(): string|null
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        return "https://maps.google.com/?q={$this->latitude},{$this->longitude}";
    }

    /**
     * Google Maps embed URL — for displaying a map pin in a WebView.
     * Uses the Maps Embed API key from geocoder config.
     *
     * ✅ Generated from latitude + longitude — no DB column needed.
     * Returns null if coordinates are not set yet.
     *
     * Usage: $branch->map_embed_url
     */
    public function getMapEmbedUrlAttribute(): string|null
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        $key = config('geocoder.key');

        return "https://www.google.com/maps/embed/v1/place?key={$key}&q={$this->latitude},{$this->longitude}";
    }

    /**
     * Google Maps directions URL — opens navigation to this branch.
     * Mobile app passes user's current location as the origin.
     *
     * ✅ Generated from latitude + longitude — no DB column needed.
     * Returns null if coordinates are not set yet.
     *
     * Usage: $branch->directions_url
     * In mobile app: open this URL to launch navigation
     */
    public function getDirectionsUrlAttribute(): string|null
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        return "https://maps.google.com/maps/dir/?api=1&destination={$this->latitude},{$this->longitude}";
    }

    /**
     * Total medicine units in stock across all medicines in this branch.
     * Sums branch_medicine.quantity for all medicines.
     *
     * Usage: $branch->total_stock
     */
    public function getTotalStockAttribute(): int
    {
        return $this->medicines()->sum('branch_medicine.quantity');
    }

    /**
     * True if this branch has any medicine with 0 < quantity <= 10.
     * Used to show a low-stock warning badge on manager dashboards.
     *
     * Usage: $branch->has_low_stock
     */
    public function getHasLowStockAttribute(): bool
    {
        return $this->medicines()
            ->wherePivot('quantity', '>', 0)
            ->wherePivot('quantity', '<=', 10)
            ->exists();
    }

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    /**
     * Branch belongs to one pharmacy.
     * From migration: branches.pharmacy_id → FK → cascadeOnDelete.
     *
     * Usage: $branch->pharmacy
     */
    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    /**
     * Branch belongs to one city.
     * From migration: branches.city_id → FK → cascadeOnDelete.
     *
     * Usage: $branch->city
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Branch belongs to one governorate through city.
     * Chain: Branch (city_id) → City (governorate_id) → Governorate
     *
     * Uses hasOneThrough — single SQL JOIN, no N+1.
     *
     * Usage: $branch->governorate
     */
    public function governorate(): HasOneThrough
    {
        return $this->hasOneThrough(
            Governorate::class, // target model
            City::class,        // intermediate model
            'id',               // FK on cities → matches branch.city_id
            'id',               // FK on governorates → matches city.governorate_id
            'city_id',          // local key on branches
            'governorate_id'    // local key on cities
        );
    }

    /**
     * Branch country — accessed through the city → governorate chain.
     *
     * Laravel hasOneThrough only supports ONE intermediate table.
     * Country is two levels deep so we cannot use it directly.
     *
     * ✅ Use eager loading instead:
     *   $branch->load('city.governorate.country')
     *   $branch->city->governorate->country
     *
     * ❌ Do NOT call $branch->country() as a relation — it won't work.
     */

    /**
     * Staff users assigned to this branch (pharmacy owners + managers).
     * Clients have branch_id = null → excluded automatically.
     * From migration: users.branch_id FK (nullable) → onDelete('set null')
     *
     * Usage: $branch->users
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Only branch managers assigned to this branch.
     * Uses Spatie permission role name 'branch-manager'.
     *
     * Usage: $branch->managers
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
     * Branch belongs to many medicines via branch_medicine pivot.
     *
     * Pivot columns:
     *   quantity    → current stock count in this branch.
     *   price       → branch-level price override (overrides medicine_price).
     *   expiry_date → expiry date of the current stock batch.
     *
     * ⚠️ Ensure your branch_medicine migration includes:
     *   $table->decimal('price', 8, 2)->nullable();
     *   $table->date('expiry_date')->nullable();
     *
     * Usage: $branch->medicines
     *        $branch->medicines()->where('branch_medicine.quantity', '>', 0)->get()
     */
    public function medicines(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, 'branch_medicine')
            ->withPivot('quantity', 'price', 'expiry_date')
            ->withTimestamps();
    }

    // ──────────────────────────────────────────
    // Helpers — instance methods for business logic
    // ──────────────────────────────────────────

    /**
     * Check if this branch stocks a specific medicine (any quantity).
     *
     * Usage: $branch->hasMedicine(5)         → bool
     *        $branch->hasMedicine($medicine) → bool
     */
    public function hasMedicine(Medicine|int $medicine): bool
    {
        $id = $medicine instanceof Medicine ? $medicine->id : $medicine;

        return $this->medicines()
            ->where('medicines.id', $id)
            ->exists();
    }

    /**
     * Check if this branch has available stock (quantity > 0) for a medicine.
     *
     * Usage: $branch->hasMedicineInStock(5)         → bool
     *        $branch->hasMedicineInStock($medicine) → bool
     */
    public function hasMedicineInStock(Medicine|int $medicine): bool
    {
        $id = $medicine instanceof Medicine ? $medicine->id : $medicine;

        return $this->medicines()
            ->where('medicines.id', $id)
            ->wherePivot('quantity', '>', 0)
            ->exists();
    }

    /**
     * Get the stock quantity of a specific medicine in this branch.
     * Returns 0 if the medicine is not stocked here.
     *
     * Usage: $branch->stockOf(5)         → int
     *        $branch->stockOf($medicine) → int
     */
    public function stockOf(Medicine|int $medicine): int
    {
        $id = $medicine instanceof Medicine ? $medicine->id : $medicine;

        $pivot = $this->medicines()
            ->where('medicines.id', $id)
            ->first();

        return $pivot ? (int) $pivot->pivot->quantity : 0;
    }

    /**
     * Get all low-stock medicines in this branch (0 < quantity <= threshold).
     * Returns a query builder — can be chained and paginated.
     *
     * Usage: $branch->lowStockMedicines()->get()
     *        $branch->lowStockMedicines(5)->with('category')->get()
     */
    public function lowStockMedicines(int $threshold = 10): BelongsToMany
    {
        return $this->medicines()
            ->wherePivot('quantity', '>', 0)
            ->wherePivot('quantity', '<=', $threshold);
    }

    /**
     * Get medicines expiring within N days in this branch.
     * Returns a query builder — can be chained and paginated.
     *
     * Usage: $branch->expiringSoonMedicines()->get()
     *        $branch->expiringSoonMedicines(7)->with('category')->get()
     */
    public function expiringSoonMedicines(int $days = 30): BelongsToMany
    {
        return $this->medicines()
            ->wherePivotBetween('expiry_date', [
                now()->toDateString(),
                now()->addDays($days)->toDateString(),
            ]);
    }

    // ──────────────────────────────────────────
    // Scopes — Location
    // ──────────────────────────────────────────

    /**
     * Branches in a specific city.
     * Direct column filter — fastest possible query.
     *
     * Usage: Branch::inCity(3)->get();
     */
    public function scopeInCity(Builder $query, int $cityId): Builder
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Branches in a specific governorate through city.
     *
     * Usage: Branch::inGovernorate(2)->get();
     */
    public function scopeInGovernorate(Builder $query, int $governorateId): Builder
    {
        return $query->whereHas(
            'city',
            fn(Builder $q) =>
            $q->where('governorate_id', $governorateId)
        );
    }

    /**
     * Branches in a specific country through city → governorate.
     *
     * Usage: Branch::inCountry(1)->get();
     */
    public function scopeInCountry(Builder $query, int $countryId): Builder
    {
        return $query->whereHas(
            'city.governorate',
            fn(Builder $q) =>
            $q->where('country_id', $countryId)
        );
    }

    /**
     * Branches within a radius using the Haversine formula.
     * Adds a computed 'distance' column and orders nearest first.
     * Always chain withCoordinates() before this scope.
     *
     * Usage: Branch::withCoordinates()->nearby(30.0444, 31.2357, 5)->get();
     */
    public function scopeNearby(Builder $query, float $lat, float $lng, int $radiusKm = 5): Builder
    {
        return $query
            ->selectRaw(
                "*, (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?)) * sin(radians(latitude))
                )) AS distance",
                [$lat, $lng, $lat]
            )
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance');
    }

    /**
     * Only branches with valid coordinates (not null).
     * Always chain before nearby() to avoid null math errors.
     *
     * Usage: Branch::withCoordinates()->nearby(30.0444, 31.2357)->get();
     */
    public function scopeWithCoordinates(Builder $query): Builder
    {
        return $query->whereNotNull('latitude')
            ->whereNotNull('longitude');
    }

    // ──────────────────────────────────────────
    // Scopes — Pharmacy
    // ──────────────────────────────────────────

    /**
     * Branches belonging to a specific pharmacy.
     *
     * Usage: Branch::ofPharmacy(1)->get();
     */
    public function scopeOfPharmacy(Builder $query, int $pharmacyId): Builder
    {
        return $query->where('pharmacy_id', $pharmacyId);
    }

    // ──────────────────────────────────────────
    // Scopes — Stock
    // ──────────────────────────────────────────

    /**
     * Branches that stock a specific medicine (any quantity).
     *
     * Usage: Branch::hasMedicine(5)->get();
     */
    public function scopeHasMedicine(Builder $query, int $medicineId): Builder
    {
        return $query->whereHas(
            'medicines',
            fn(Builder $q) =>
            $q->where('medicines.id', $medicineId)
        );
    }

    /**
     * Branches with available stock (quantity > 0) for a specific medicine.
     * Primary scope for client "where can I buy this medicine?" feature.
     *
     * Usage: Branch::hasMedicineInStock(5)->get();
     */
    public function scopeHasMedicineInStock(Builder $query, int $medicineId): Builder
    {
        return $query->whereHas(
            'medicines',
            fn(Builder $q) =>
            $q->where('medicines.id', $medicineId)
                ->where('branch_medicine.quantity', '>', 0)
        );
    }

    /**
     * Branches with at least one medicine at or below the stock threshold.
     * Used for branch manager and pharmacy owner dashboard alerts.
     *
     * Usage: Branch::withLowStock()->get();
     *        Branch::withLowStock(5)->get();
     */
    public function scopeWithLowStock(Builder $query, int $threshold = 10): Builder
    {
        return $query->whereHas(
            'medicines',
            fn(Builder $q) =>
            $q->where('branch_medicine.quantity', '>', 0)
                ->where('branch_medicine.quantity', '<=', $threshold)
        );
    }

    /**
     * Branches with at least one medicine expiring within N days.
     *
     * Usage: Branch::withExpiringSoon()->get();
     *        Branch::withExpiringSoon(7)->get();
     */
    public function scopeWithExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->whereHas(
            'medicines',
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
     * Recently created branches within the last N days.
     *
     * Usage: Branch::recent()->get();
     *        Branch::recent(7)->get();
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Order branches by translated name in current or given locale.
     * Uses driver-aware JSON extract — works on MySQL, MariaDB, SQLite, PostgreSQL.
     *
     * Usage: Branch::orderByName()->get();
     *        Branch::orderByName('desc', 'ar')->get();
     */
    public function scopeOrderByName(Builder $query, string $direction = 'asc', ?string $locale = null): Builder
    {
        $locale ??= app()->getLocale();

        $extract = match (\Illuminate\Support\Facades\DB::getDriverName()) {
            'sqlite' => "JSON_EXTRACT(branch_name, '$.{$locale}')",
            'pgsql'  => "branch_name->>'$.{$locale}'",
            default  => "JSON_UNQUOTE(JSON_EXTRACT(branch_name, '$.{$locale}'))",
        };

        return $query->orderByRaw("{$extract} {$direction}");
    }
}
