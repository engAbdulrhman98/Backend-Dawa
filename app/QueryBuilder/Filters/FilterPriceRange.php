<?php

namespace App\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

// class FilterPriceRange implements Filter
// {
//     public function __invoke(Builder $query, $value, string $property)
//     {
//         // Spatie QueryBuilder automatically converts comma-separated values into an array
//         if (is_array($value) && count($value) >= 2) {
//             $min = (float) $value[0];
//             $max = (float) $value[1];

//             // Ensure min is actually less than or equal to max
//             if ($min <= $max) {
//                 $query->whereBetween('medicine_price', [$min, $max]);
//             }
//         }
//     }
// }
//**------------------------- */

/**
 * Custom filter for price range using spatie/laravel-query-builder.
 *
 * Why custom instead of AllowedFilter::scope()?
 * scopePriceRange() takes TWO parameters ($min, $max) but
 * AllowedFilter::scope() passes the value as ONE string — broken.
 * This class splits the comma-separated string into two floats.
 *
 * ──────────────────────────────────────────────────────────────
 * Registration in controller:
 * ──────────────────────────────────────────────────────────────
 *   AllowedFilter::custom('price_range', new FilterPriceRange())
 *
 * ──────────────────────────────────────────────────────────────
 * Client usage:
 * ──────────────────────────────────────────────────────────────
 *   GET /api/v1/medicines?filter[price_range]=10,50
 *   → returns medicines with medicine_price between 10 and 50
 *
 * ──────────────────────────────────────────────────────────────
 * File location:
 * ──────────────────────────────────────────────────────────────
 *   app/QueryBuilder/Filters/FilterPriceRange.php
 */
class FilterPriceRange implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        // Accepts: ?filter[price_range]=10,50
        // Splits into: min=10.0, max=50.0
        $parts = array_map('trim', explode(',', $value));

        // Silently ignore malformed input — needs at least min,max
        if (count($parts) < 2) {
            return;
        }

        $min = (float) $parts[0];
        $max = (float) $parts[1];

        // Swap silently if client passes them in wrong order
        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        $query->priceRange($min, $max);
    }
}
