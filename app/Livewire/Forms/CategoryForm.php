<?php

namespace App\Livewire\Forms;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

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

    public function save(): Category
    {
        $this->slug = $this->slug !== '' ? Str::slug($this->slug) : Str::slug($this->name);

        $this->validate();

        $attrs = $this->except('id');

        if ($this->id) {
            $category = Category::findOrFail($this->id);
            $category->update($attrs);

            return $category->fresh();
        }

        return Category::create($attrs);
    }
}
