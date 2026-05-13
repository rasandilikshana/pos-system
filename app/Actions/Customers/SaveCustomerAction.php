<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\DB;

class SaveCustomerAction
{
    public function __construct(private readonly CustomerRepository $customers) {}

    /**
     * @param  array<string, mixed>  $attrs
     */
    public function execute(array $attrs, ?int $id = null): Customer
    {
        return DB::transaction(fn () => $id
            ? $this->customers->update($id, $attrs)
            : $this->customers->create($attrs)
        );
    }
}
