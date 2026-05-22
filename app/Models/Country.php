<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasTranslations, HasSlug;

    protected $fillable = [
        'country_name',
        'country_slug',
    ];

    /**
     * Translatable attributes.
     */
    public array $translatable = ['country_name'];

    /**
     * Cast the country_name JSON column.
     */
    protected $casts = [
        'country_name' => 'array',
    ];

     // -------------------------------------------------------------------------
    // Slug & Route
    // -------------------------------------------------------------------------

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (Country $model) => $model->getTranslation('country_name', 'en'))
            ->saveSlugsTo('country_slug')
            ->slugsShouldBeNoLongerThan(80);
    }

    public function getRouteKeyName(): string
    {
        return 'country_slug';
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function governorates(): HasMany
    {
        return $this->hasMany(Governorate::class);
    }

    // -------------------------------------------------------------------------
    // Local Scopes — reusable query constraints called manually
    // Usage: Country::searchByName('egypt')->get()
    // -------------------------------------------------------------------------

    /**
     * Search by English or Arabic name (case-insensitive LIKE).
     */
    public function scopeSearchByName(Builder $query, string $value): Builder
    {
        return $query->where(function (Builder $q) use ($value) {
            $q->where('country_name->en', 'like', "%{$value}%")
              ->orWhere('country_name->ar', 'like', "%{$value}%");
        });
    }

    /**
     * Filter by exact slug.
     */
    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('country_slug', $slug);
    }

    /**
     * Order alphabetically by English name.
     */
    public function scopeOrderByNameEn(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(country_name, '$.en')) {$direction}");
    }

    /**
     * Order alphabetically by Arabic name.
     */
    public function scopeOrderByNameAr(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(country_name, '$.ar')) {$direction}");
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
     * Only countries that have at least one governorate.
     */
    public function scopeHasGovernorates(Builder $query): Builder
    {
        return $query->has('governorates');
    }

    /**
     * Only countries that have no governorates yet.
     */
    public function scopeWithoutGovernorates(Builder $query): Builder
    {
        return $query->doesntHave('governorates');
    }

    /**
     * With governorates count eager-loaded.
     */
    public function scopeWithGovernoratesCount(Builder $query): Builder
    {
        return $query->withCount('governorates');
    }

    // -------------------------------------------------------------------------
    // Dynamic Scopes — accept parameters to change behaviour
    // Usage: Country::createdBetween('2024-01-01', '2024-12-31')->get()
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
        return $query->where("country_name->{$locale}", $value);
    }

    /**
     * Search by name in a specific locale only.
     */
    public function scopeSearchByLocale(Builder $query, string $locale, string $value): Builder
    {
        return $query->where("country_name->{$locale}", 'like', "%{$value}%");
    }

    /**
     * Paginate with a custom per-page, falling back to a default.
     */
    public function scopePaginateWith(Builder $query, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }
}
