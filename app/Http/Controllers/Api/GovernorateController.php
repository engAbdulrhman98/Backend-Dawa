<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Governorate\StoreGovernorateRequest;
use App\Http\Requests\Governorate\UpdateGovernorateRequest;
use App\Http\Resources\GovernorateResource;
use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GovernorateController extends Controller
{
    /**
     * NESTED: GET /api/v1/countries/{country}/governorates
     * Lists governorates scoped to the given country.
     *
     * ?filter[governorate_name]=Cairo
     * ?filter[governorate_slug]=cairo
     * ?sort=governorate_slug,-created_at
     * ?per_page=15
     */
    public function index(?Country $country = null): AnonymousResourceCollection
    {
        $baseQuery = $country
            ? Governorate::query()->where('country_id', $country->id)
            : Governorate::query();

        $governorates = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                AllowedFilter::callback('governorate_name', function ($query, $value) {
                    $query->where('governorate_name->en', 'like', "%{$value}%")
                        ->orWhere('governorate_name->ar', 'like', "%{$value}%");
                }),
                AllowedFilter::exact('governorate_slug'),
                AllowedFilter::exact('country_id'),
            ])
            ->allowedSorts(['governorate_slug', 'created_at', 'updated_at'])
            ->defaultSort('governorate_slug')
            ->paginate(request()->integer('per_page', 100))
            ->appends(request()->query());

        return GovernorateResource::collection($governorates);
    }

    /**
     * NON-NESTED: GET /api/v1/governorates
     * Lists ALL governorates — optionally filtered by country_id.
     *
     * ?filter[governorate_name]=Cairo
     * ?filter[governorate_slug]=cairo
     * ?filter[country_id]=1
     * ?sort=governorate_slug,-created_at
     * ?per_page=15
     */
    public function indexAll(): AnonymousResourceCollection
    {
        $governorates = QueryBuilder::for(Governorate::class)
            ->allowedFilters([
                AllowedFilter::callback('governorate_name', function ($query, $value) {
                    $query->where('governorate_name->en', 'like', "%{$value}%")
                        ->orWhere('governorate_name->ar', 'like', "%{$value}%");
                }),
                AllowedFilter::exact('governorate_slug'),
                AllowedFilter::exact('country_id'),
            ])
            ->allowedSorts(['governorate_slug', 'created_at', 'updated_at'])
            ->defaultSort('governorate_slug')
            ->paginate(request()->integer('per_page', 100))
            ->appends(request()->query());

        return GovernorateResource::collection($governorates);
    }

    /**
     * NESTED: POST /api/v1/countries/{country}/governorates
     *
     * Body (form-data or JSON):
     *   governorate_name[en] = Cairo Governorate
     *   governorate_name[ar] = محافظة القاهرة
     */
    public function store(StoreGovernorateRequest $request, Country $country): JsonResponse
    {
        $validated = $request->validated();

        $governorate = new Governorate();
        $governorate->country_id = $country->id;
        $governorate->setTranslations('governorate_name', $validated['governorate_name']);
        $governorate->save();

        return response()->json(new GovernorateResource($governorate), 201);
    }

    /**
     * NON-NESTED (shallow): GET /api/v1/governorates/{governorate}
     * With shallow(), show/update/destroy receive ONLY the child — no parent.
     */
    public function show(Governorate $governorate): JsonResponse
    {
        return response()->json(new GovernorateResource($governorate));
    }

    /**
     * NON-NESTED (shallow): PUT/PATCH /api/v1/governorates/{governorate}
     *
     * Body (form-data or JSON):
     *   governorate_name[en] = Cairo Governorate Updated
     *   governorate_name[ar] = محافظة القاهرة محدثة
     */
    public function update(UpdateGovernorateRequest $request, Governorate $governorate): JsonResponse
    {
        $validated = $request->validated();

        $existing = $governorate->getTranslations('governorate_name');
        $merged = array_merge($existing, $validated['governorate_name']);
        $governorate->setTranslations('governorate_name', $merged);
        $governorate->save();

        return response()->json(new GovernorateResource($governorate->fresh()));
    }

    /**
     * NON-NESTED (shallow): DELETE /api/v1/governorates/{governorate}
     */
    public function destroy(Governorate $governorate): JsonResponse
    {
        $governorate->delete();

        return response()->json([
            'message' => __('governorates.messages.deleted'),
        ]);
    }
}
