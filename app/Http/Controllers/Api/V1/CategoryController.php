<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Categories\SaveCategoryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCategoryRequest;
use App\Http\Requests\Api\V1\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function index(Request $request, CategoryRepository $categories): AnonymousResourceCollection
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        return CategoryResource::collection(
            $categories->paginateFiltered(
                search: (string) $request->string('q'),
                perPage: $request->integer('per_page', 25),
            )
        );
    }

    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category);
    }

    public function store(StoreCategoryRequest $request, SaveCategoryAction $save): JsonResponse
    {
        $category = $save->execute($request->validated());

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    public function update(UpdateCategoryRequest $request, Category $category, SaveCategoryAction $save): CategoryResource
    {
        $updated = $save->execute($request->validated(), $category->id);

        return new CategoryResource($updated);
    }

    public function destroy(Category $category, CategoryRepository $categories): JsonResponse
    {
        $categories->delete($category->id);

        return response()->json(null, 204);
    }
}
