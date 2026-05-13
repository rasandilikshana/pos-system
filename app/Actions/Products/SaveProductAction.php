<?php

namespace App\Actions\Products;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;

class SaveProductAction
{
    public function __construct(private readonly ProductRepository $products) {}

    /**
     * @param  array<string, mixed>  $attrs
     */
    public function execute(array $attrs, ?int $id = null): Product
    {
        return DB::transaction(function () use ($attrs, $id) {
            $product = $id
                ? $this->products->update($id, $attrs)
                : $this->products->create($attrs);

            return $product->fresh(['category', 'supplier']);
        });
    }
}
