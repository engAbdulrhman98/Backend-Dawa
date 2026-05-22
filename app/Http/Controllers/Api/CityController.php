<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\City\StoreCityRequest;
use App\Http\Requests\City\UpdateCityRequest;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Models\Governorate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CityController extends Controller
{
    /**
     * NESTED: GET /api/v1/governorates/{governorate}/cities
     * Lists cities scoped to the given governorate.
     *
     * ?filter[city_name]=Nasr
     * ?filter[city_slug]=nasr-city
     * ?sort=city_slug,-created_at
     * ?per_page=15
     */
    public function index(?Governorate $governorate = null): AnonymousResourceCollection
    {
        $baseQuery = $governorate
            ? City::query()->where('governorate_id', $governorate->id)
            : City::query();

        $cities = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                AllowedFilter::callback('city_name', function ($query, $value) {
                    $query->where('city_name->en', 'like', "%{$value}%")
                        ->orWhere('city_name->ar', 'like', "%{$value}%");
                }),
                AllowedFilter::exact('city_slug'),
                AllowedFilter::exact('governorate_id'),
            ])
            ->allowedSorts(['city_slug', 'created_at', 'updated_at'])
            ->defaultSort('city_slug')
            ->paginate(request()->integer('per_page', 100))
            ->appends(request()->query());

        return CityResource::collection($cities);
    }

    /**
     * NON-NESTED: GET /api/v1/cities
     * Lists ALL cities — optionally filtered by governorate_id or country.
     *
     * ?filter[city_name]=Nasr
     * ?filter[city_slug]=nasr-city
     * ?filter[governorate_id]=1
     * ?sort=city_slug,-created_at
     * ?per_page=15
     */
    public function indexAll(): AnonymousResourceCollection
    {
        $cities = QueryBuilder::for(City::class)
            ->allowedFilters([
                AllowedFilter::callback('city_name', function ($query, $value) {
                    $query->where('city_name->en', 'like', "%{$value}%")
                        ->orWhere('city_name->ar', 'like', "%{$value}%");
                }),
                AllowedFilter::exact('city_slug'),
                AllowedFilter::exact('governorate_id'),
            ])
            ->allowedSorts(['city_slug', 'created_at', 'updated_at'])
            ->defaultSort('city_slug')
            ->paginate(request()->integer('per_page', 15))
            ->appends(request()->query());

        return CityResource::collection($cities);
    }

    /**
     * NESTED: POST /api/v1/governorates/{governorate}/cities
     *
     * Body (form-data or JSON):
     *   city_name[en] = Nasr City
     *   city_name[ar] = مدينة نصر
     */
    public function store(StoreCityRequest $request, Governorate $governorate): JsonResponse
    {
        $validated = $request->validated();

        $city = new City();
        $city->governorate_id = $governorate->id;
        $city->setTranslations('city_name', $validated['city_name']);
        $city->save();

        return response()->json(new CityResource($city), 201);
    }

    /**
     * NON-NESTED (shallow): GET /api/v1/cities/{city}
     * With shallow(), show/update/destroy receive ONLY the child — no parent.
     */
    public function show(City $city): JsonResponse
    {
        return response()->json(new CityResource($city));
    }

    /**
     * NON-NESTED (shallow): PUT/PATCH /api/v1/cities/{city}
     *
     * Body (form-data or JSON):
     *   city_name[en] = Nasr City Updated
     *   city_name[ar] = مدينة نصر محدثة
     */
    public function update(UpdateCityRequest $request, City $city): JsonResponse
    {
        $validated = $request->validated();

        $existing = $city->getTranslations('city_name');
        $merged = array_merge($existing, $validated['city_name']);
        $city->setTranslations('city_name', $merged);
        $city->save();

        return response()->json(new CityResource($city->fresh()));
    }

    /**
     * NON-NESTED (shallow): DELETE /api/v1/cities/{city}
     */
    public function destroy(City $city): JsonResponse
    {
        $city->delete();

        return response()->json([
            'message' => __('cities.messages.deleted'),
        ]);
    }
}
