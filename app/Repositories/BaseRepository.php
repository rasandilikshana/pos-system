<?php

namespace App\Repositories;

use App\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    /** @return class-string<Model> */
    abstract protected function model(): string;

    public function query(): Builder
    {
        return ($this->model())::query();
    }

    public function findById(int $id, array $with = []): ?Model
    {
        return $this->query()->with($with)->find($id);
    }

    public function findByIdOrFail(int $id, array $with = []): Model
    {
        return $this->query()->with($with)->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return ($this->model())::create($data)->fresh();
    }

    public function update(int $id, array $data): Model
    {
        $model = $this->findByIdOrFail($id);
        $model->update($data);

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->findByIdOrFail($id)->delete();
    }

    public function paginate(int $perPage = 20, array $with = []): LengthAwarePaginator
    {
        return $this->query()->with($with)->paginate($perPage);
    }

    public function list(array $with = []): Collection
    {
        return $this->query()->with($with)->get();
    }

    public function exists(int $id): bool
    {
        return $this->query()->whereKey($id)->exists();
    }
}
