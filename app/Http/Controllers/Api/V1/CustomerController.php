<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Customers\SaveCustomerAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCustomerRequest;
use App\Http\Requests\Api\V1\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends Controller
{
    public function index(Request $request, CustomerRepository $customers): AnonymousResourceCollection
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        return CustomerResource::collection(
            $customers->paginateFiltered((string) $request->string('q'), $request->integer('per_page', 25))
        );
    }

    public function show(Customer $customer): CustomerResource
    {
        return new CustomerResource($customer);
    }

    public function store(StoreCustomerRequest $request, SaveCustomerAction $save): JsonResponse
    {
        $customer = $save->execute($request->validated());

        return (new CustomerResource($customer))->response()->setStatusCode(201);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer, SaveCustomerAction $save): CustomerResource
    {
        return new CustomerResource($save->execute($request->validated(), $customer->id));
    }

    public function destroy(Customer $customer, CustomerRepository $customers): JsonResponse
    {
        $customers->delete($customer->id);

        return response()->json(null, 204);
    }
}
