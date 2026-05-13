<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Products\SaveProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreProductRequest;
use App\Http\Requests\Api\V1\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request, ProductRepository $products): AnonymousResourceCollection
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'barcode' => ['nullable', 'string', 'max:32'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if ($request->filled('barcode')) {
            return ProductResource::collection(
                Product::query()
                    ->with('category')
                    ->active()
                    ->where('barcode', $request->string('barcode'))
                    ->paginate($request->integer('per_page', 25))
            );
        }

        return ProductResource::collection(
            $products->paginateFiltered(
                search: (string) $request->string('q'),
                categoryId: $request->integer('category_id') ?: null,
                activeOnly: true,
                perPage: $request->integer('per_page', 25),
            )
        );
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load('category'));
    }

    public function store(StoreProductRequest $request, SaveProductAction $save): JsonResponse
    {
        $product = $save->execute($request->validated());

        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    public function update(UpdateProductRequest $request, Product $product, SaveProductAction $save): ProductResource
    {
        return new ProductResource($save->execute($request->validated(), $product->id));
    }

    public function destroy(Product $product, ProductRepository $products): JsonResponse
    {
        $products->delete($product->id);

        return response()->json(null, 204);
    }
}
