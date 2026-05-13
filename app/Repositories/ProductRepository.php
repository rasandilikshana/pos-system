<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository extends BaseRepository
{
    protected function model(): string
    {
        return Product::class;
    }

    public function paginateFiltered(string $search = '', ?int $categoryId = null, bool $activeOnly = false, int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->with('category')
            ->when($activeOnly, fn ($q) => $q->where('is_active', true))
            ->when($search !== '', function ($q) use ($search) {
                $like = '%'.$search.'%';
                $q->where(fn ($qq) => $qq->where('name', 'like', $like)
                    ->orWhere('sku', 'like', $like)
                    ->orWhere('barcode', 'like', $like));
            })
            ->when($categoryId, fn ($q, $id) => $q->where('category_id', $id))
            ->orderBy('name')
            ->paginate($perPage);
    }
}
