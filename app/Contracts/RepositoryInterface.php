<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    public function query(): Builder;

    public function findById(int $id, array $with = []): ?Model;

    public function findByIdOrFail(int $id, array $with = []): Model;

    public function create(array $data): Model;

    public function update(int $id, array $data): Model;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 20, array $with = []): LengthAwarePaginator;

    public function list(array $with = []): Collection;

    public function exists(int $id): bool;
}
