<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Schema;

class CategoryController extends Controller
{
    private bool $hasMedicinesTable;

    public function __construct()
    {
        $this->hasMedicinesTable = Schema::hasTable('medicines');
    }

    /**
     * Display a listing of the resource.
     */
    //* done
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = QueryBuilder::for(Category::class)
            ->allowedFilters([
                AllowedFilter::scope('name', 'byName'),
                AllowedFilter::scope('slug', 'bySlug'),
                AllowedFilter::scope('has_medicines', 'hasMedicines'),
                AllowedFilter::scope('recent', 'recent'),
            ])
            ->allowedSorts(['created_at', 'updated_at'])
            ->allowedIncludes($this->hasMedicinesTable ? ['medicines'] : [])
            ->orderByName();

        if ($this->hasMedicinesTable) {
            $query->withCount('medicines');
        }

        $categories = $query
            ->paginate($request->integer('per_page', 100))
            ->withQueryString();

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): CategoryResource
    {
        $validated = $request->validated();

        $category = Category::create([
            'category_name' => $validated['category_name'],
            'category_description' => $validated['category_description'] ?? null,
        ]);

        if ($this->hasMedicinesTable) {
            $category->loadCount('medicines');
        }

        return new CategoryResource($category);
    }
    /**
     * Display the specified resource.
     */
    //* done
    public function show(Category $category): CategoryResource
    {
        if ($this->hasMedicinesTable) {
            $category->loadCount('medicines');

            if (request()->boolean('with_medicines')) {
                $category->load('medicines');
            }
        }

        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $category->update($request->validated());

        $category = $category->fresh();

        if ($this->hasMedicinesTable) {
            $category->loadCount('medicines');
        }

        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */

    //* done */
    public function destroy(Category $category): JsonResponse
    {
        if ($this->hasMedicinesTable && $category->medicines()->exists()) {
            return response()->json([
                'message' => __('categories.messages.has_medicines'),
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => __('categories.messages.deleted'),
        ]);
    }
}
