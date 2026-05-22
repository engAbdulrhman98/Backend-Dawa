<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pharmacy\StorePharmacyRequest;
use App\Http\Requests\Pharmacy\UpdatePharmacyRequest;
use App\Http\Resources\BranchResource;
use App\Http\Resources\MedicineResource;
use App\Http\Resources\PharmacyResource;
use App\Models\Pharmacy;
use App\QueryBuilder\Filters\FilterNearby;
use App\QueryBuilder\Sorts\SortByTranslatedName;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class PharmacyController extends Controller
{
    /**
     * Display a listing of all pharmacies.
     * GET /api/v1/pharmacies
     *
     * Available filters:
     *   ?filter[in_city]=3                        → inCity
     *   ?filter[in_governorate]=2                 → inGovernorate
     *   ?filter[in_country]=1                     → inCountry
     *   ?filter[nearby]=30.0444,31.2357,5         → nearby (lat,lng,radiusKm)
     *   ?filter[with_branches]=1                  → withBranches
     *   ?filter[has_medicine]=5                   → hasMedicine
     *   ?filter[has_medicine_in_stock]=5          → hasMedicineInStock
     *   ?filter[low_stock][medicine_id]=5         → lowStock
     *   ?filter[with_expiring_soon]=7             → withExpiringSoon (days)
     *   ?filter[recent]=7                         → recent (days)
     *
     * Available sorts:
     *   ?sort=created_at / -created_at
     *   ?sort=updated_at / -updated_at
     *   ?sort=name / -name                        → locale-aware translated name
     *
     * Available includes:
     *   ?include=branches
     *   ?include=branches.city
     *   ?include=branches.city.governorate
     *   ?include=owners
     *   ?include=managers
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $pharmacies = QueryBuilder::for(Pharmacy::class)
            ->allowedFilters([
                // ── Location ──────────────────────────────────
                AllowedFilter::scope('in_city', 'inCity'),
                AllowedFilter::scope('in_governorate', 'inGovernorate'),
                AllowedFilter::scope('in_country', 'inCountry'),
                AllowedFilter::custom('nearby', new FilterNearby()),

                // ── Existence ─────────────────────────────────
                AllowedFilter::scope('with_branches', 'withBranches'),
                AllowedFilter::scope('no_branches', 'noBranches'),

                // ── Medicine & Stock ───────────────────────────
                AllowedFilter::scope('has_medicine', 'hasMedicine'),
                AllowedFilter::scope('has_medicine_in_stock', 'hasMedicineInStock'),
                AllowedFilter::scope('with_expiring_soon', 'withExpiringSoon'),

                // ── Recency ───────────────────────────────────
                AllowedFilter::scope('recent', 'recent'),
            ])
            ->allowedSorts([
                'created_at',
                'updated_at',
                AllowedSort::custom('name', new SortByTranslatedName('pharmacy_name')),
            ])
            ->allowedIncludes([
                'branches',
                'branches.city',
                'branches.city.governorate',
                'owners',
                'managers',
            ])
            ->when(
                !$request->has('sort'),
                fn($q) => $q->orderByName()
            )
            ->paginate($request->integer('per_page', 100))
            ->withQueryString();

        return PharmacyResource::collection($pharmacies);
    }

    /**
     * Store a new pharmacy.
     * POST /api/v1/pharmacies
     * Auth: super-admin only (enforced in StorePharmacyRequest)
     */
    public function store(StorePharmacyRequest $request): PharmacyResource
    {
        $pharmacy = Pharmacy::create($request->validated());

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename = $pharmacy->pharmacy_slug . '.' . $extension;
            $pharmacy->addMediaFromRequest('image')
                ->usingFileName($filename)
                ->toMediaCollection('pharmacies');
        }

        return new PharmacyResource($pharmacy);
    }

    /**
     * Display a specific pharmacy.
     * GET /api/v1/pharmacies/{pharmacy}  (pharmacy = pharmacy_slug)
     */
    public function show(Pharmacy $pharmacy): PharmacyResource
    {
        $pharmacy->loadMissing([
            'branches.city',
            'branches.city.governorate',
        ]);

        return new PharmacyResource($pharmacy);
    }

    /**
     * Update an existing pharmacy.
     * PUT/PATCH /api/v1/pharmacies/{pharmacy}
     * Auth: super-admin or pharmacy-owner (own pharmacy only)
     */
    public function update(UpdatePharmacyRequest $request, Pharmacy $pharmacy): PharmacyResource
    {
        $pharmacy->update($request->validated());

        if ($request->hasFile('image')) {
            $pharmacy->clearMediaCollection('pharmacies');
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename = $pharmacy->pharmacy_slug . '.' . $extension;
            $pharmacy->addMediaFromRequest('image')
                ->usingFileName($filename)
                ->toMediaCollection('pharmacies');
        }

        return new PharmacyResource($pharmacy);
    }

    /**
     * Delete a pharmacy.
     * DELETE /api/v1/pharmacies/{pharmacy}
     * Auth: super-admin only
     *
     * Cascade: branches are deleted → branch_medicine pivot cleared
     * Users with this pharmacy_id get pharmacy_id set to null (setNull)
     */
    public function destroy(Pharmacy $pharmacy): JsonResponse
    {
        $pharmacy->clearMediaCollection('pharmacies');
        $pharmacy->delete();

        return response()->json([
            'message' => __('pharmacy.messages.deleted'),
        ]);
    }

    /**
     * Get all medicines available across all branches of a pharmacy.
     * GET /api/v1/pharmacies/{pharmacy}/medicines
     *
     * Uses $pharmacy->medicines() query builder — not a direct relation.
     * Supports all Medicine model scopes as filters.
     *
     * Available filters:
     *   ?filter[in_stock]=1
     *   ?filter[name]=para
     *   ?filter[max_price]=30
     *   ?filter[expiring_soon]=7
     */
    public function medicines(Request $request, Pharmacy $pharmacy): AnonymousResourceCollection
    {
        $medicines = $pharmacy->medicines()
            ->when($request->has('filter.in_stock'), fn($q) => $q->inStock())
            ->when(
                $request->filled('filter.name'),
                fn($q) => $q->byName($request->input('filter.name'))
            )
            ->when(
                $request->filled('filter.max_price'),
                fn($q) => $q->maxPrice((float) $request->input('filter.max_price'))
            )
            ->when(
                $request->filled('filter.expiring_soon'),
                fn($q) => $q->expiringSoon((int) $request->input('filter.expiring_soon'))
            )
            ->with('category')
            ->orderByName()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return MedicineResource::collection($medicines);
    }

    /**
     * Get low stock medicines for a pharmacy (owner dashboard).
     * GET /api/v1/pharmacies/{pharmacy}/low-stock
     * Auth: super-admin or pharmacy-owner (own pharmacy only)
     */
    public function lowStock(Request $request, Pharmacy $pharmacy): AnonymousResourceCollection
    {
        $this->authorizePharmacyAccess($pharmacy);

        $threshold = $request->integer('threshold', 10);

        $medicines = $pharmacy->lowStockMedicines($threshold)
            ->with('category')
            ->orderByName()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return MedicineResource::collection($medicines);
    }

    /**
     * Get expiring soon medicines for a pharmacy (owner dashboard).
     * GET /api/v1/pharmacies/{pharmacy}/expiring-soon
     * Auth: super-admin or pharmacy-owner (own pharmacy only)
     */
    public function expiringSoon(Request $request, Pharmacy $pharmacy): AnonymousResourceCollection
    {
        $this->authorizePharmacyAccess($pharmacy);

        $days = $request->integer('days', 30);

        $medicines = $pharmacy->expiringSoonMedicines($days)
            ->with('category')
            ->orderByName()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return MedicineResource::collection($medicines);
    }

    /**
     * Get the nearest branch to given coordinates for this pharmacy.
     * GET /api/v1/pharmacies/{pharmacy}/nearest-branch?lat=30.04&lng=31.23
     */
    public function nearestBranch(Request $request, Pharmacy $pharmacy): JsonResponse
    {
        $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $branch = $pharmacy->nearestBranchTo(
            lat: $request->float('lat'),
            lng: $request->float('lng'),
        );

        if (!$branch) {
            return response()->json([
                'message' => __('pharmacy.messages.no_branch_found'),
            ], 404);
        }

        return response()->json([
            'data' => new BranchResource($branch),
        ]);
    }

    /**
     * Search pharmacies by name across both locales.
     * GET /api/v1/pharmacies/search?q=cairo&locale=en
     *
     * Combines name search with optional location and stock filters
     * so a client can search "pharmacy near me that has paracetamol".
     *
     * Query parameters:
     *   q        → required, min 1 char — partial name match
     *   locale   → optional, 'en' or 'ar' — defaults to app locale
     *   city_id  → optional, filter by city
     *   medicine → optional, filter by medicine id in stock
     *   nearby   → optional, "lat,lng,radius" — e.g. "30.04,31.23,5"
     *   per_page → optional, default 15
     *
     * Usage:
     *   GET /api/v1/pharmacies/search?q=cairo
     *   GET /api/v1/pharmacies/search?q=صيدلية&locale=ar
     *   GET /api/v1/pharmacies/search?q=cairo&city_id=3&medicine=5
     *   GET /api/v1/pharmacies/search?q=cairo&nearby=30.04,31.23,5
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'q'        => ['required', 'string', 'min:1', 'max:100'],
            'locale'   => ['nullable', 'string', 'in:en,ar'],
            'city_id'  => ['nullable', 'integer', 'exists:cities,id'],
            'medicine' => ['nullable', 'integer', 'exists:medicines,id'],
            'nearby'   => ['nullable', 'string'],
        ]);

        $locale = $request->input('locale') ?: app()->getLocale();

        // Build driver-aware JSON extract for name search
        $driver  = \Illuminate\Support\Facades\DB::getDriverName();
        $extract = match ($driver) {
            'sqlite' => "JSON_EXTRACT(pharmacy_name, '$.{$locale}')",
            'pgsql'  => "pharmacy_name->>'$.{$locale}'",
            default  => "JSON_UNQUOTE(JSON_EXTRACT(pharmacy_name, '$.{$locale}'))",
        };

        $pharmacies = QueryBuilder::for(Pharmacy::class)
            // ── Name search ───────────────────────────────────
            ->whereRaw("{$extract} LIKE ?", ['%' . $request->string('q') . '%'])

            // ── Optional: filter by city ──────────────────────
            ->when(
                $request->filled('city_id'),
                fn($q) => $q->inCity((int) $request->input('city_id'))
            )

            // ── Optional: filter by medicine in stock ─────────
            ->when(
                $request->filled('medicine'),
                fn($q) => $q->hasMedicineInStock((int) $request->input('medicine'))
            )

            // ── Optional: filter by coordinates + radius ──────
            // Accepts: ?nearby=30.0444,31.2357,5
            ->when($request->filled('nearby'), function ($q) use ($request) {
                $parts = array_map('trim', explode(',', $request->input('nearby')));

                if (count($parts) >= 2) {
                    $lat    = (float) $parts[0];
                    $lng    = (float) $parts[1];
                    $radius = isset($parts[2]) ? (int) $parts[2] : 5;

                    if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                        $q->nearby($lat, $lng, $radius);
                    }
                }
            })

            ->allowedIncludes([
                'branches',
                'branches.city',
            ])

            // ── Order by name in searched locale ──────────────
            ->orderByName('asc', $locale)

            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return PharmacyResource::collection($pharmacies);
    }

    // ──────────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────────

    /**
     * Abort with 403 if the authenticated user is not allowed
     * to access this pharmacy's protected data.
     * Super-admin passes always. Pharmacy owner must own this pharmacy.
     */
    private function authorizePharmacyAccess(Pharmacy $pharmacy): void
    {
        $user = request()->user();

        if ($user->hasRole('super-admin')) {
            return;
        }

        if ($user->hasRole('pharmacy-owner') && (int) $user->pharmacy_id === (int) $pharmacy->id) {
            return;
        }

        abort(403, __('pharmacy.messages.unauthorized'));
    }
}
