<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Suppliers\SaveSupplierAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreSupplierRequest;
use App\Http\Requests\Api\V1\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Repositories\SupplierRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupplierController extends Controller
{
    public function index(Request $request, SupplierRepository $suppliers): AnonymousResourceCollection
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        return SupplierResource::collection(
            $suppliers->paginateFiltered((string) $request->string('q'), $request->integer('per_page', 25))
        );
    }

    public function show(Supplier $supplier): SupplierResource
    {
        return new SupplierResource($supplier);
    }

    public function store(StoreSupplierRequest $request, SaveSupplierAction $save): JsonResponse
    {
        $supplier = $save->execute($request->validated());

        return (new SupplierResource($supplier))->response()->setStatusCode(201);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier, SaveSupplierAction $save): SupplierResource
    {
        return new SupplierResource($save->execute($request->validated(), $supplier->id));
    }

    public function destroy(Supplier $supplier, SupplierRepository $suppliers): JsonResponse
    {
        $suppliers->delete($supplier->id);

        return response()->json(null, 204);
    }
}
