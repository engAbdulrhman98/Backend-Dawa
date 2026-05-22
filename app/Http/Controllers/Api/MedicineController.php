<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medicine\StoreMedicineRequest;
use App\Http\Requests\Medicine\UpdateMedicineRequest;
use App\Http\Resources\MedicineResource;
use App\Models\Category;
use App\Models\Medicine;
use App\QueryBuilder\Filters\FilterNearby;
use App\QueryBuilder\Filters\FilterPriceRange;
use App\QueryBuilder\Sorts\SortByTranslatedName;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class MedicineController extends Controller
{
    /**
     * Display all medicines across all categories.
     * GET /api/v1/medicines
     *
     * Available filters:
     *   ?filter[name]=para                        → byName (locale-aware)
     *   ?filter[slug]=paracetamol                 → bySlug
     *   ?filter[category]=2                       → byCategory (id)
     *   ?filter[category_slug]=antibiotics        → byCategorySlug
     *   ?filter[in_branch]=3                      → inBranch
     *   ?filter[in_pharmacy]=1                    → inPharmacy
     *   ?filter[in_city]=5                        → inCity
     *   ?filter[nearby]=30.0444,31.2357,5         → nearby (lat,lng,radiusKm)
     *   ?filter[in_stock]=1                       → inStock
     *   ?filter[out_of_stock]=1                   → outOfStock
     *   ?filter[low_stock]=10                     → lowStock (threshold)
     *   ?filter[expiring_before]=2025-12-31       → expiringBefore
     *   ?filter[expiring_soon]=7                  → expiringSoon (days)
     *   ?filter[recent]=7                         → recent (days)
     *   ?filter[price_range]=10,50                → priceRange (min,max)
     *   ?filter[max_price]=30                     → maxPrice
     *   ?filter[min_price]=10                     → minPrice
     *   ?filter[available_at_price]=25            → availableAtPrice
     *
     * Available sorts:
     *   ?sort=created_at / -created_at
     *   ?sort=updated_at / -updated_at
     *   ?sort=medicine_price / -medicine_price
     *   ?sort=name / -name                        → locale-aware translated name
     *
     * Available includes:
     *   ?include=category
     *   ?include=branches
     *   ?include=branches.pharmacy
     *   ?include=branches.city
     *   ?include=media
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $medicines = QueryBuilder::for(Medicine::class)
            ->allowedFilters([
                // ── Search ────────────────────────────────────
                AllowedFilter::scope('name', 'byName'),
                AllowedFilter::scope('slug', 'bySlug'),

                // ── Category ──────────────────────────────────
                AllowedFilter::scope('category', 'byCategory'),
                AllowedFilter::scope('category_slug', 'byCategorySlug'),

                // ── Location ──────────────────────────────────
                AllowedFilter::scope('in_branch', 'inBranch'),
                AllowedFilter::scope('in_pharmacy', 'inPharmacy'),
                AllowedFilter::scope('in_city', 'inCity'),
                AllowedFilter::custom('nearby', new FilterNearby()),

                // ── Stock ─────────────────────────────────────
                AllowedFilter::scope('in_stock', 'inStock'),
                AllowedFilter::scope('out_of_stock', 'outOfStock'),
                AllowedFilter::scope('low_stock', 'lowStock'),

                // ── Price ─────────────────────────────────────
                AllowedFilter::custom('price_range', new FilterPriceRange()),
                AllowedFilter::scope('max_price', 'maxPrice'),
                AllowedFilter::scope('min_price', 'minPrice'),
                AllowedFilter::scope('available_at_price', 'availableAtPrice'),

                // ── Expiry ────────────────────────────────────
                AllowedFilter::scope('expiring_before', 'expiringBefore'),
                AllowedFilter::scope('expiring_soon', 'expiringSoon'),

                // ── Recency ───────────────────────────────────
                AllowedFilter::scope('recent', 'recent'),
            ])
            ->allowedSorts([
                'created_at',
                'updated_at',
                'medicine_price',
                AllowedSort::custom('name', new SortByTranslatedName('medicine_name')),
            ])
            ->allowedIncludes([
                'category',
                'branches',
                'branches.pharmacy',
                'branches.city',
                'media',
            ])
            ->when(
                !$request->has('sort'),
                fn($q) => $q->orderByName()
            )
            ->paginate($request->integer('per_page', 100))
            ->withQueryString();

        return MedicineResource::collection($medicines);
    }

    /**
     * Display medicines under a specific category.
     * GET /api/v1/categories/{category}/medicines
     */
    public function indexByCategory(Request $request, Category $category): AnonymousResourceCollection
    {
        $medicines = QueryBuilder::for(Medicine::class)
            ->where('category_id', $category->id)
            ->allowedFilters([
                // ── Search ────────────────────────────────────
                AllowedFilter::scope('name', 'byName'),
                AllowedFilter::scope('slug', 'bySlug'),

                // ── Location ──────────────────────────────────
                AllowedFilter::scope('in_branch', 'inBranch'),
                AllowedFilter::scope('in_pharmacy', 'inPharmacy'),
                AllowedFilter::scope('in_city', 'inCity'),
                AllowedFilter::custom('nearby', new FilterNearby()),

                // ── Stock ─────────────────────────────────────
                AllowedFilter::scope('in_stock', 'inStock'),
                AllowedFilter::scope('out_of_stock', 'outOfStock'),
                AllowedFilter::scope('low_stock', 'lowStock'),

                // ── Price ─────────────────────────────────────
                AllowedFilter::custom('price_range', new FilterPriceRange()),
                AllowedFilter::scope('max_price', 'maxPrice'),
                AllowedFilter::scope('min_price', 'minPrice'),
                AllowedFilter::scope('available_at_price', 'availableAtPrice'),

                // ── Expiry ────────────────────────────────────
                AllowedFilter::scope('expiring_before', 'expiringBefore'),
                AllowedFilter::scope('expiring_soon', 'expiringSoon'),

                // ── Recency ───────────────────────────────────
                AllowedFilter::scope('recent', 'recent'),
            ])
            ->allowedSorts([
                'created_at',
                'updated_at',
                'medicine_price',
                AllowedSort::custom('name', new SortByTranslatedName('medicine_name')),
            ])
            ->allowedIncludes([
                'category',
                'branches',
                'branches.pharmacy',
                'branches.city',
                'media',
            ])
            ->when(
                !$request->has('sort'),
                fn($q) => $q->orderByName()
            )
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return MedicineResource::collection($medicines);
    }

    /**
     * Store a new medicine.
     * POST /api/v1/medicines
     */
    public function store(StoreMedicineRequest $request): MedicineResource
    {
        $validated = $request->validated();
        $branchIds = $request->input('branches', []);
        unset($validated['branches']);

        $medicine = Medicine::create($validated);

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename = $medicine->medicine_slug . '.' . $extension;
            $medicine->addMediaFromRequest('image')
                ->usingFileName($filename)
                ->toMediaCollection('medicines');
        }

        if (!empty($branchIds)) {
            $medicine->branches()->sync($branchIds);
        }

        $medicine->load(['category', 'branches']);

        return new MedicineResource($medicine);
    }

    /**
     * Display a specific medicine.
     * GET /api/v1/medicines/{medicine}   (medicine = medicine_slug)
     */
    public function show(Medicine $medicine): MedicineResource
    {
        $medicine->loadMissing(['category', 'branches.pharmacy', 'branches.city']);

        return new MedicineResource($medicine);
    }

    /**
     * Update an existing medicine.
     * PUT/PATCH /api/v1/medicines/{medicine}
     */
    public function update(UpdateMedicineRequest $request, Medicine $medicine): MedicineResource
    {
        $validated = $request->validated();
        $branchIds = $request->input('branches', []);
        unset($validated['branches']);

        $medicine->update($validated);

        if ($request->hasFile('image')) {
            $medicine->clearMediaCollection('medicines');
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename = $medicine->medicine_slug . '.' . $extension;
            $medicine->addMediaFromRequest('image')
                ->usingFileName($filename)
                ->toMediaCollection('medicines');
        }

        if ($request->has('branches')) {
            $medicine->branches()->sync($branchIds);
        }

        $medicine->load(['category', 'branches']);

        return new MedicineResource($medicine);
    }

    /**
     * Delete a medicine.
     * DELETE /api/v1/medicines/{medicine}
     */
    public function destroy(Medicine $medicine): JsonResponse
    {
        $medicine->clearMediaCollection('medicines');
        $medicine->delete();

        return response()->json([
            'message' => __('medicine.messages.deleted'),
        ]);
    }

    /**
     * Search medicines by name across both locales.
     * GET /api/v1/medicines/search?q=para&locale=en
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'q'      => ['required', 'string', 'min:1', 'max:100'],
            'locale' => ['nullable', 'string', 'in:en,ar'],
        ]);

        $medicines = QueryBuilder::for(Medicine::class)
            ->byName($request->string('q')->toString(), $request->input('locale'))
            ->allowedFilters([
                AllowedFilter::scope('in_city', 'inCity'),
                AllowedFilter::scope('in_pharmacy', 'inPharmacy'),
                AllowedFilter::scope('in_stock', 'inStock'),
                AllowedFilter::scope('max_price', 'maxPrice'),
                AllowedFilter::custom('nearby', new FilterNearby()),
            ])
            ->allowedIncludes(['category', 'branches', 'branches.pharmacy'])
            ->orderByName(locale: $request->input('locale'))
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return MedicineResource::collection($medicines);
    }
}
