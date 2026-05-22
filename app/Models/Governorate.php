<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Governorate extends Model
{
    use HasTranslations, HasSlug;

    protected $fillable = [
        'country_id',
        'governorate_name',
        'governorate_slug',
    ];

    public array $translatable = ['governorate_name'];

    protected $casts = [
        'governorate_name' => 'array',
    ];

    // -------------------------------------------------------------------------
    // Slug & Route
    // -------------------------------------------------------------------------

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn(Governorate $model) => $model->getTranslation('governorate_name', 'en'))
            ->saveSlugsTo('governorate_slug')
            ->slugsShouldBeNoLongerThan(80);
    }

    public function getRouteKeyName(): string
    {
        return 'governorate_slug';
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    // -------------------------------------------------------------------------
    // Local Scopes
    // Usage: Governorate::searchByName('cairo')->get()
    // -------------------------------------------------------------------------

    /**
     * Search by English or Arabic name (case-insensitive LIKE).
     */
    public function scopeSearchByName(Builder $query, string $value): Builder
    {
        return $query->where(function (Builder $q) use ($value) {
            $q->where('governorate_name->en', 'like', "%{$value}%")
                ->orWhere('governorate_name->ar', 'like', "%{$value}%");
        });
    }

    /**
     * Filter by exact slug.
     */
    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('governorate_slug', $slug);
    }

    /**
     * Filter by country id.
     */
    public function scopeForCountry(Builder $query, int $countryId): Builder
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Order alphabetically by English name.
     */
    public function scopeOrderByNameEn(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(governorate_name, '$.en')) {$direction}");
    }

    /**
     * Order alphabetically by Arabic name.
     */
    public function scopeOrderByNameAr(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(governorate_name, '$.ar')) {$direction}");
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
     * Only governorates that have at least one city.
     */
    public function scopeHasCities(Builder $query): Builder
    {
        return $query->has('cities');
    }

    /**
     * Only governorates that have no cities yet.
     */
    public function scopeWithoutCities(Builder $query): Builder
    {
        return $query->doesntHave('cities');
    }

    /**
     * With cities count eager-loaded.
     */
    public function scopeWithCitiesCount(Builder $query): Builder
    {
        return $query->withCount('cities');
    }

    /**
     * With country relationship eager-loaded.
     */
    public function scopeWithCountry(Builder $query): Builder
    {
        return $query->with('country');
    }

    // -------------------------------------------------------------------------
    // Dynamic Scopes
    // Usage: Governorate::createdBetween('2024-01-01', '2024-12-31')->get()
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
        return $query->where("governorate_name->{$locale}", $value);
    }

    /**
     * Search by name in a specific locale only.
     */
    public function scopeSearchByLocale(Builder $query, string $locale, string $value): Builder
    {
        return $query->where("governorate_name->{$locale}", 'like', "%{$value}%");
    }

    /**
     * Paginate with a custom per-page, falling back to a default.
     */
    public function scopePaginateWith(Builder $query, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }
}
