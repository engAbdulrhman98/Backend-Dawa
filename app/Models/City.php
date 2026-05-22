<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;
class City extends Model
{
    use HasTranslations, HasSlug;

    protected $fillable = [
        'governorate_id',
        'city_name',
        'city_slug',
    ];

    public array $translatable = ['city_name'];

    protected $casts = [
        'city_name' => 'array',
    ];
    // -------------------------------------------------------------------------
    // Slug & Route
    // -------------------------------------------------------------------------

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn(City $model) => $model->getTranslation('city_name', 'en'))
            ->saveSlugsTo('city_slug')
            ->slugsShouldBeNoLongerThan(80);
    }

    public function getRouteKeyName(): string
    {
        return 'city_slug';
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    // -------------------------------------------------------------------------
    // Local Scopes
    // Usage: City::searchByName('nasr')->get()
    // -------------------------------------------------------------------------

    /**
     * Search by English or Arabic name (case-insensitive LIKE).
     */
    public function scopeSearchByName(Builder $query, string $value): Builder
    {
        return $query->where(function (Builder $q) use ($value) {
            $q->where('city_name->en', 'like', "%{$value}%")
                ->orWhere('city_name->ar', 'like', "%{$value}%");
        });
    }

    /**
     * Filter by exact slug.
     */
    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('city_slug', $slug);
    }

    /**
     * Filter by governorate id.
     */
    public function scopeForGovernorate(Builder $query, int $governorateId): Builder
    {
        return $query->where('governorate_id', $governorateId);
    }

    /**
     * Filter by country through governorate relationship.
     */
    public function scopeForCountry(Builder $query, int $countryId): Builder
    {
        return $query->whereHas('governorate', function (Builder $q) use ($countryId) {
            $q->where('country_id', $countryId);
        });
    }

    /**
     * Order alphabetically by English name.
     */
    public function scopeOrderByNameEn(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(city_name, '$.en')) {$direction}");
    }

    /**
     * Order alphabetically by Arabic name.
     */
    public function scopeOrderByNameAr(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(city_name, '$.ar')) {$direction}");
    }

    /**
     * Most recently created first.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Oldest created first.
     */
    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Only cities that have at least one branch.
     */
    public function scopeHasBranches(Builder $query): Builder
    {
        return $query->has('branches');
    }

    /**
     * Only cities that have no branches yet.
     */
    public function scopeWithoutBranches(Builder $query): Builder
    {
        return $query->doesntHave('branches');
    }

    /**
     * With branches count eager-loaded.
     */
    public function scopeWithBranchesCount(Builder $query): Builder
    {
        return $query->withCount('branches');
    }

    /**
     * With governorate relationship eager-loaded.
     */
    public function scopeWithGovernorate(Builder $query): Builder
    {
        return $query->with('governorate');
    }

    /**
     * With governorate and country eager-loaded (full chain).
     */
    public function scopeWithFullChain(Builder $query): Builder
    {
        return $query->with('governorate.country');
    }

    // -------------------------------------------------------------------------
    // Dynamic Scopes
    // Usage: City::createdBetween('2024-01-01', '2024-12-31')->get()
    // -------------------------------------------------------------------------

    /**
     * Filter by creation date range.
     */
    public function scopeCreatedBetween(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Filter by exact locale translation value.
     */
    public function scopeWhereTranslation(Builder $query, string $locale, string $value): Builder
    {
        return $query->where("city_name->{$locale}", $value);
    }

    /**
     * Search by name in a specific locale only.
     */
    public function scopeSearchByLocale(Builder $query, string $locale, string $value): Builder
    {
        return $query->where("city_name->{$locale}", 'like', "%{$value}%");
    }

    /**
     * Paginate with a custom per-page, falling back to a default.
     */
    public function scopePaginateWith(Builder $query, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }
}