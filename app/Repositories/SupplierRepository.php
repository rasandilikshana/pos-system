<?php

namespace App\Repositories;

use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierRepository extends BaseRepository
{
    protected function model(): string
    {
        return Supplier::class;
    }

    public function paginateFiltered(string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->when($search !== '', function ($q) use ($search) {
                $like = '%'.$search.'%';
                $q->where(fn ($qq) => $qq->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like));
            })
            ->orderBy('name')
            ->paginate($perPage);
    }
}
