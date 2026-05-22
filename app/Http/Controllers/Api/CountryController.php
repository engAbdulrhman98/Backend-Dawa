<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Country\UpdateCountryRequest;
use App\Http\Requests\Country\StoreCountryRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CountryController extends Controller
{
    /**
     * GET /api/v1/countries
     *
     * Supported query params (via spatie/laravel-query-builder):
     *   ?filter[country_name]=Egypt
     *   ?sort=country_slug,-created_at
     *   ?include=...   (add relationships here if needed)
     */
    public function index(): AnonymousResourceCollection
    {
        $countries = QueryBuilder::for(Country::class)
            ->allowedFilters([
                AllowedFilter::callback('country_name', function ($query, $value) {
                    $query->where('country_name->en', 'like', "%{$value}%")
                        ->orWhere('country_name->ar', 'like', "%{$value}%");
                }),
                AllowedFilter::exact('country_slug'),
            ])
            ->allowedSorts(['country_slug', 'created_at', 'updated_at'])
            ->defaultSort('country_slug')
            ->paginate(request()->integer('per_page', 100))
            ->appends(request()->query());

        return CountryResource::collection($countries);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCountryRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $country = new Country();
        $country->setTranslations('country_name', $validated['country_name']);
        $country->save();

        return response()->json(new CountryResource($country), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country): CountryResource
    {
        return new CountryResource($country);
    }


    /**
     * Update the specified resource in storage.
     */


        public function update(UpdateCountryRequest $request, Country $country): JsonResponse
    {
        $validated = $request->validated();

        $existing = $country->getTranslations('country_name');
        $merged   = array_merge($existing, $validated['country_name']);
        $country->setTranslations('country_name', $merged);
        $country->save();

        return response()->json(new CountryResource($country->fresh()));
    }

    /**
     * DELETE /api/v1/countries/{country}
     */
    public function destroy(Country $country): JsonResponse
    {
        $country->delete();

        return response()->json([
            'message' => __('countries.messages.deleted'),
        ], 200);
    }
}
