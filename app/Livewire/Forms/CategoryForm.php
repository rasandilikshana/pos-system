<?php

namespace App\Livewire\Forms;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

/** Form Object for create/edit Category — validation + data shaping only. Persistence lives in SaveCategoryAction. */
class CategoryForm extends Form
{
    public ?int $id = null;

    #[Validate('required|string|max:120')]
    public string $name = '';

    public string $slug = '';

    #[Validate('nullable|string|max:255')]
    public ?string $description = null;

    public bool $is_active = true;

    public function setCategory(?Category $category): void
    {
        if (! $category) {
            return;
        }

        $this->fill([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'is_active' => (bool) $category->is_active,
        ]);
    }

    public function rules(): array
    {
        $slugForUniqueness = $this->slug !== '' ? Str::slug($this->slug) : Str::slug($this->name);

        return [
            'slug' => [
                'required', 'string', 'max:120',
                Rule::unique('categories', 'slug')->ignore($this->id)->whereNull('deleted_at'),
            ],
            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('categories', 'name')->ignore($this->id)->whereNull('deleted_at'),
            ],
        ];
    }

    /** @return array<string, mixed> */
    public function attributes(): array
    {
        $this->slug = $this->slug !== '' ? Str::slug($this->slug) : Str::slug($this->name);

        $this->validate();

        return $this->except('id');
    }
}
