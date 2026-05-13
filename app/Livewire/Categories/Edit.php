<?php

namespace App\Livewire\Categories;

use App\Livewire\Forms\CategoryForm;
use App\Models\Category;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Edit Category')]
class Edit extends Component
{
    public CategoryForm $form;

    public ?Category $category = null;

    public function mount(?Category $category = null): void
    {
        abort_unless(auth()->user()?->can('categories.manage'), 403);

        if ($category?->exists) {
            $this->category = $category;
            $this->form->setCategory($category);
        }
    }

    public function save(): mixed
    {
        $this->form->save();
        session()->flash('status', $this->category ? __('Category updated.') : __('Category created.'));

        return redirect()->route('categories.index');
    }

    public function render(): View
    {
        return view('livewire.categories.edit');
    }
}
