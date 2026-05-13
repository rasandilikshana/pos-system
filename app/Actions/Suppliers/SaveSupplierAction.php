<?php

namespace App\Actions\Suppliers;

use App\Models\Supplier;
use App\Repositories\SupplierRepository;
use Illuminate\Support\Facades\DB;

class SaveSupplierAction
{
    public function __construct(private readonly SupplierRepository $suppliers) {}

    /**
     * @param  array<string, mixed>  $attrs
     */
    public function execute(array $attrs, ?int $id = null): Supplier
    {
        return DB::transaction(fn () => $id
            ? $this->suppliers->update($id, $attrs)
            : $this->suppliers->create($attrs)
        );
    }
}
