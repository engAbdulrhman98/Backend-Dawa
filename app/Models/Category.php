<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    use HasSlug, HasTranslations;

    protected $fillable = [
        'category_name',
        'category_description',
        'category_slug',
    ];

    public array $translatable = [
        'category_name',
        'category_description',
    ];

    // ──────────────────────────────────────────
    // Sluggable
    // ──────────────────────────────────────────

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn(Category $model) => $model->getTranslation('category_name', 'en'))
            ->saveSlugsTo('category_slug')
            ->slugsShouldBeNoLongerThan(80);
    }

    public function getRouteKeyName(): string
    {
        return 'category_slug';
    }

    // ──────────────────────────────────────────
    // Relations
    // ──────────────────────────────────────────

    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class);
    }

    // ──────────────────────────────────────────
    // JSON extract helper — driver aware
    // ──────────────────────────────────────────

    private static function jsonExtract(string $column, string $locale): string
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return "JSON_EXTRACT({$column}, '$.{$locale}')";
        }

        if ($driver === 'pgsql') {
            return "{$column}->>'$.{$locale}'";
        }

        // mysql / mariadb
        return "JSON_UNQUOTE(JSON_EXTRACT({$column}, '$.{$locale}'))";
    }

    // ──────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────

    public function scopeByName(Builder $query, string $name, ?string $locale = null): Builder
    {
        $locale ??= app()->getLocale();
        $extract = self::jsonExtract('category_name', $locale);

        return $query->whereRaw("{$extract} LIKE ?", ["%{$name}%"]);
    }

    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('category_slug', $slug);
    }

    public function scopeHasMedicines(Builder $query): Builder
    {
        return $query->has('medicines');
    }

    public function scopeWithoutMedicines(Builder $query): Builder
    {
        return $query->doesntHave('medicines');
    }

    public function scopeOrderByName(Builder $query, string $direction = 'asc', ?string $locale = null): Builder
    {
        $locale ??= app()->getLocale();
        $extract = self::jsonExtract('category_name', $locale);

        return $query->orderByRaw("{$extract} {$direction}");
    }

    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
