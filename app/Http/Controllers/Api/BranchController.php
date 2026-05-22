<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Http\Resources\BranchResource;
use App\Http\Resources\MedicineResource;
use App\Models\Branch;
use App\Models\Pharmacy;
use App\QueryBuilder\Filters\FilterNearby;
use App\QueryBuilder\Sorts\SortByTranslatedName;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    /**
     * Display all branches.
     * GET /api/v1/branches
     *
     * Available filters:
     *   ?filter[in_city]=3
     *   ?filter[in_governorate]=2
     *   ?filter[in_country]=1
     *   ?filter[of_pharmacy]=1
     *   ?filter[has_medicine]=5
     *   ?filter[has_medicine_in_stock]=5
     *   ?filter[with_low_stock]=10
     *   ?filter[with_expiring_soon]=7
     *   ?filter[with_coordinates]=1
     *   ?filter[nearby]=30.0444,31.2357,5
     *   ?filter[recent]=7
     *
     * Available sorts:
     *   ?sort=created_at / -created_at
     *   ?sort=updated_at / -updated_at
     *   ?sort=name / -name
     *
     * Available includes:
     *   ?include=pharmacy
     *   ?include=city
     *   ?include=governorate
     *   ?include=managers
     *   ?include=medicines
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $branches = QueryBuilder::for(Branch::class)
            ->allowedFilters([
                // ── Location ──────────────────────────────────
                AllowedFilter::scope('in_city', 'inCity'),
                AllowedFilter::scope('in_governorate', 'inGovernorate'),
                AllowedFilter::scope('in_country', 'inCountry'),
                AllowedFilter::scope('with_coordinates', 'withCoordinates'),
                AllowedFilter::custom('nearby', new FilterNearby()),

                // ── Pharmacy ──────────────────────────────────
                AllowedFilter::scope('of_pharmacy', 'ofPharmacy'),

                // ── Stock ─────────────────────────────────────
                AllowedFilter::scope('has_medicine', 'hasMedicine'),
                AllowedFilter::scope('has_medicine_in_stock', 'hasMedicineInStock'),
                AllowedFilter::scope('with_low_stock', 'withLowStock'),
                AllowedFilter::scope('with_expiring_soon', 'withExpiringSoon'),

                // ── Recency ───────────────────────────────────
                AllowedFilter::scope('recent', 'recent'),
            ])
            ->allowedSorts([
                'created_at',
                'updated_at',
                AllowedSort::custom('name', new SortByTranslatedName('branch_name')),
            ])
            ->allowedIncludes([
                'pharmacy',
                'city',
                'governorate',
                'managers',
                'medicines',
            ])
            ->when(
                !$request->has('sort'),
                fn($q) => $q->orderByName()
            )
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return BranchResource::collection($branches);
    }

    /**
     * Display branches belonging to a specific pharmacy.
     * GET /api/v1/pharmacies/{pharmacy}/branches
     */
    public function indexByPharmacy(Request $request, Pharmacy $pharmacy): AnonymousResourceCollection
    {
        $branches = QueryBuilder::for(Branch::class)
            ->where('pharmacy_id', $pharmacy->id)
            ->allowedFilters([
                AllowedFilter::scope('in_city', 'inCity'),
                AllowedFilter::scope('in_governorate', 'inGovernorate'),
                AllowedFilter::scope('with_coordinates', 'withCoordinates'),
                AllowedFilter::custom('nearby', new FilterNearby()),
                AllowedFilter::scope('has_medicine_in_stock', 'hasMedicineInStock'),
                AllowedFilter::scope('with_low_stock', 'withLowStock'),
                AllowedFilter::scope('with_expiring_soon', 'withExpiringSoon'),
            ])
            ->allowedSorts([
                'created_at',
                AllowedSort::custom('name', new SortByTranslatedName('branch_name')),
            ])
            ->allowedIncludes(['city', 'governorate', 'managers', 'medicines'])
            ->when(
                !$request->has('sort'),
                fn($q) => $q->orderByName()
            )
            ->paginate($request->integer('per_page', 100))
            ->withQueryString();

        return BranchResource::collection($branches);
    }

    /**
     * Store a new branch.
     * POST /api/v1/branches
     * Auth: super-admin or pharmacy-owner (own pharmacy only)
     *
     * Note: latitude + longitude are auto-filled from branch_address
     * by spatie/geocoder via Branch::booted() if not provided.
     */
    public function store(StoreBranchRequest $request): BranchResource
    {
        $branch = Branch::create($request->validated());

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename = $branch->branch_slug . '.' . $extension;
            $branch->addMediaFromRequest('image')
                ->usingFileName($filename)
                ->toMediaCollection('branches');
        }

        $branch->load(['pharmacy', 'city']);

        return new BranchResource($branch);
    }

    /**
     * Display a specific branch.
     * GET /api/v1/branches/{branch}  (branch = branch_slug)
     */
    public function show(Branch $branch): BranchResource
    {
        $branch->loadMissing([
            'pharmacy',
            'city',
            'governorate',
        ]);

        return new BranchResource($branch);
    }

    /**
     * Update a branch.
     * PUT/PATCH /api/v1/branches/{branch}
     * Auth: super-admin or pharmacy-owner (own pharmacy only)
     *
     * Pass refresh_coordinates=true to re-geocode after address update.
     */
    public function update(UpdateBranchRequest $request, Branch $branch): BranchResource
    {
        $validated = $request->validated();

        if ($request->boolean('refresh_coordinates')) {
            $branch->fill($validated);
            $branch->refreshCoordinates()->save();
        } else {
            $branch->update($validated);
        }

        if ($request->hasFile('image')) {
            $branch->clearMediaCollection('branches');
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename = $branch->branch_slug . '.' . $extension;
            $branch->addMediaFromRequest('image')
                ->usingFileName($filename)
                ->toMediaCollection('branches');
        }

        $branch->load(['pharmacy', 'city']);

        return new BranchResource($branch);
    }

    /**
     * Delete a branch.
     * DELETE /api/v1/branches/{branch}
     * Auth: super-admin or pharmacy-owner (own pharmacy only)
     */
    public function destroy(Branch $branch): JsonResponse
    {
        $this->authorizeBranchAccess($branch);

        $branch->clearMediaCollection('branches');
        $branch->delete();

        return response()->json([
            'message' => __('branch.messages.deleted'),
        ]);
    }

    /**
     * Search branches by name across both locales.
     * GET /api/v1/branches/search?q=cairo&locale=en
     *
     * Query parameters:
     *   q        → required, min 1 char — partial name match
     *   locale   → optional, 'en' or 'ar' — defaults to app locale
     *   city_id  → optional, filter by city
     *   medicine → optional, filter by medicine id in stock
     *   nearby   → optional, "lat,lng,radius"
     *   per_page → optional, default 15
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

        $extract = match (DB::getDriverName()) {
            'sqlite' => "JSON_EXTRACT(branch_name, '$.{$locale}')",
            'pgsql'  => "branch_name->>'$.{$locale}'",
            default  => "JSON_UNQUOTE(JSON_EXTRACT(branch_name, '$.{$locale}'))",
        };

        $branches = QueryBuilder::for(Branch::class)
            ->whereRaw("{$extract} LIKE ?", ['%' . $request->string('q') . '%'])
            ->when(
                $request->filled('city_id'),
                fn($q) => $q->inCity((int) $request->input('city_id'))
            )
            ->when(
                $request->filled('medicine'),
                fn($q) => $q->hasMedicineInStock((int) $request->input('medicine'))
            )
            ->when($request->filled('nearby'), function ($q) use ($request) {
                $parts = array_map('trim', explode(',', $request->input('nearby')));

                if (count($parts) >= 2) {
                    $lat    = (float) $parts[0];
                    $lng    = (float) $parts[1];
                    $radius = isset($parts[2]) ? (int) $parts[2] : 5;

                    if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                        $q->withCoordinates()->nearby($lat, $lng, $radius);
                    }
                }
            })
            ->allowedIncludes(['pharmacy', 'city', 'medicines'])
            ->orderByName('asc', $locale)
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return BranchResource::collection($branches);
    }

    /**
     * Get nearby branches based on user coordinates.
     * GET /api/v1/branches/nearby?lat=30.0444&lng=31.2357&radius=5
     *
     * Returns branches ordered by distance (nearest first).
     * Each result includes distance_km in the resource.
     */
    public function nearby(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'lat'    => ['required', 'numeric', 'between:-90,90'],
            'lng'    => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $branches = Branch::withCoordinates()
            ->nearby(
                lat: $request->float('lat'),
                lng: $request->float('lng'),
                radiusKm: $request->integer('radius', 5)
            )
            ->with(['pharmacy', 'city'])
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return BranchResource::collection($branches);
    }

    /**
     * Get low-stock medicines in a branch.
     * GET /api/v1/branches/{branch}/low-stock?threshold=10
     * Auth: super-admin, pharmacy-owner (own pharmacy), branch-manager (own branch)
     */
    public function lowStock(Request $request, Branch $branch): AnonymousResourceCollection
    {
        $this->authorizeBranchAccess($branch);

        $medicines = $branch->lowStockMedicines($request->integer('threshold', 10))
            ->with('category')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return MedicineResource::collection($medicines);
    }

    /**
     * Get medicines expiring soon in a branch.
     * GET /api/v1/branches/{branch}/expiring-soon?days=30
     * Auth: super-admin, pharmacy-owner (own pharmacy), branch-manager (own branch)
     */
    public function expiringSoon(Request $request, Branch $branch): AnonymousResourceCollection
    {
        $this->authorizeBranchAccess($branch);

        $medicines = $branch->expiringSoonMedicines($request->integer('days', 30))
            ->with('category')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return MedicineResource::collection($medicines);
    }

    // ──────────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────────

    /**
     * Authorize branch-level protected endpoints.
     *
     * Super-admin       → always passes
     * Pharmacy-owner    → must own the pharmacy this branch belongs to
     * Branch-manager    → must be assigned to this specific branch
     */
    private function authorizeBranchAccess(Branch $branch): void
    {
        $user = request()->user();

        if ($user->hasRole('super-admin')) {
            return;
        }

        if (
            $user->hasRole('pharmacy-owner') &&
            (int) $user->pharmacy_id === (int) $branch->pharmacy_id
        ) {
            return;
        }

        if (
            $user->hasRole('branch-manager') &&
            (int) $user->branch_id === (int) $branch->id
        ) {
            return;
        }

        abort(403, __('branch.messages.unauthorized'));
    }
}
