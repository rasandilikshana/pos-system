<?php

namespace App\Actions\Categories;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaveCategoryAction
{
    public function __construct(private readonly CategoryRepository $categories) {}

    /**
     * @param  array<string, mixed>  $attrs
     */
    public function execute(array $attrs, ?int $id = null): Category
    {
        $attrs['slug'] = ! empty($attrs['slug'])
            ? Str::slug($attrs['slug'])
            : Str::slug($attrs['name'] ?? '');

        return DB::transaction(fn () => $id
            ? $this->categories->update($id, $attrs)
            : $this->categories->create($attrs)
        );
    }
}
