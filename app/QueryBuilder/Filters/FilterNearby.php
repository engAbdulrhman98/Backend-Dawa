<?php

namespace App\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

// class FilterNearby implements Filter
// {
//     public function __invoke(Builder $query, $value, string $property)
//     {
//         if (is_array($value) && count($value) >= 2) {
//             $latitude = (float) $value[0];
//             $longitude = (float) $value[1];
//             // Default to 10km radius if the 3rd parameter isn't provided
//             $radius = isset($value[2]) ? (float) $value[2] : 10;

//             $query->whereHas('branches', function (Builder $q) use ($latitude, $longitude, $radius) {
//                 // 6371 is the radius of the Earth in kilometers. Use 3959 for miles.
//                 $haversine = "(6371 * acos(cos(radians(?)) 
//                             * cos(radians(latitude)) 
//                             * cos(radians(longitude) - radians(?)) 
//                             + sin(radians(?)) 
//                             * sin(radians(latitude))))";

//                 $q->selectRaw("{$haversine} AS distance", [$latitude, $longitude, $latitude])
//                     ->whereRaw("{$haversine} <= ?", [$latitude, $longitude, $latitude, $radius]);
//             });
//         }
//     }
// }
//* ////////////////////////////////
/**
 * Custom filter for nearby location search using spatie/laravel-query-builder.
 *
 * Why custom instead of AllowedFilter::scope()?
 * scopeNearby() takes THREE parameters ($lat, $lng, $radiusKm) but
 * AllowedFilter::scope() passes the value as ONE string — broken.
 * This class splits the comma-separated string into lat, lng, radius.
 *
 * Works for both Medicine and Branch models since both have
 * a scopeNearby() that accepts the same signature.
 *
 * ──────────────────────────────────────────────────────────────
 * Registration in controller:
 * ──────────────────────────────────────────────────────────────
 *   AllowedFilter::custom('nearby', new FilterNearby())
 *
 * ──────────────────────────────────────────────────────────────
 * Client usage:
 * ──────────────────────────────────────────────────────────────
 *   GET /api/v1/medicines?filter[nearby]=30.0444,31.2357,5
 *   GET /api/v1/branches?filter[nearby]=30.0444,31.2357,10
 *
 *   Format: lat,lng,radiusKm (radius is optional — defaults to 5km)
 *
 * ──────────────────────────────────────────────────────────────
 * File location:
 * ──────────────────────────────────────────────────────────────
 *   app/QueryBuilder/Filters/FilterNearby.php
 */
class FilterNearby implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        // Accepts: ?filter[nearby]=30.0444,31.2357,5
        // Splits into: lat=30.0444, lng=31.2357, radius=5
        $parts = array_map('trim', explode(',', $value));

        // Silently ignore malformed input — needs at least lat,lng
        if (count($parts) < 2) {
            return;
        }

        $lat    = (float) $parts[0];
        $lng    = (float) $parts[1];
        $radius = isset($parts[2]) ? (int) $parts[2] : 5; // default 5km

        // Basic coordinate sanity check
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return;
        }

        // For Branch model — chain withCoordinates() to exclude null coords
        // For Medicine model — scopeNearby() goes through branches internally
        if (method_exists($query->getModel(), 'scopeWithCoordinates')) {
            $query->withCoordinates()->nearby($lat, $lng, $radius);
        } else {
            $query->nearby($lat, $lng, $radius);
        }
    }
}
