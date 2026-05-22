<?php

namespace App\QueryBuilder\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Support\Facades\DB;

// class SortByTranslatedName implements Sort
// {
//     protected ?string $column;

//     public function __construct(?string $column = null)
//     {
//         $this->column = $column;
//     }

//     public function __invoke(Builder $query, bool $descending, string $property)
//     {
//         // Use the explicitly provided column, or fall back to the property name in the request
//         $column = $this->column ?? $property;

//         // Determine the direction
//         $direction = $descending ? 'desc' : 'asc';

//         // Grab the current application locale (or you can extract it from the request if preferred)
//         $locale = request()->input('locale', app()->getLocale());

//         /*
//          * Assuming you are using Spatie Translatable (JSON columns).
//          * This uses the standard JSON extraction syntax (->) supported by MySQL and PostgreSQL.
//          */
//         $query->orderBy("{$column}->{$locale}", $direction);
//     }
// }
//* *------------------------------------
/**
 * Custom sort for translated JSON name columns.
 * Works with spatie/laravel-translatable JSON fields.
 *
 * Reusable across any model that has a translatable JSON column.
 * Pass the column name in the constructor — defaults to 'medicine_name'.
 *
 * ──────────────────────────────────────────────────────────────
 * Registration in controller:
 * ──────────────────────────────────────────────────────────────
 *   AllowedSort::custom('name', new SortByTranslatedName('medicine_name'))
 *   AllowedSort::custom('name', new SortByTranslatedName('branch_name'))
 *   AllowedSort::custom('name', new SortByTranslatedName('pharmacy_name'))
 *
 * ──────────────────────────────────────────────────────────────
 * Client usage:
 * ──────────────────────────────────────────────────────────────
 *   GET /api/v1/medicines?sort=name      → A → Z in current locale
 *   GET /api/v1/medicines?sort=-name     → Z → A in current locale
 *
 * ──────────────────────────────────────────────────────────────
 * File location:
 * ──────────────────────────────────────────────────────────────
 *   app/QueryBuilder/Sorts/SortByTranslatedName.php
 */
class SortByTranslatedName implements Sort
{
    public function __construct(
        private string $column = 'medicine_name'
    ) {}

    public function __invoke(Builder $query, bool $descending, string $property): void
    {
        $locale    = app()->getLocale();
        $direction = $descending ? 'DESC' : 'ASC';

        $extract = match (DB::getDriverName()) {
            'sqlite' => "JSON_EXTRACT({$this->column}, '$.{$locale}')",
            'pgsql'  => "{$this->column}->>'$.{$locale}'",
            default  => "JSON_UNQUOTE(JSON_EXTRACT({$this->column}, '$.{$locale}'))", // MySQL / MariaDB
        };

        $query->orderByRaw("{$extract} {$direction}");
    }
}
