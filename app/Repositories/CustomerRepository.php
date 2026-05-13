<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerRepository extends BaseRepository
{
    protected function model(): string
    {
        return Customer::class;
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
