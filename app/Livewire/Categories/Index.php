<?php

namespace App\Livewire\Categories;

use App\Repositories\CategoryRepository;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Categories')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('categories.view'), 403);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, CategoryRepository $categories): void
    {
        abort_unless(auth()->user()?->can('categories.manage'), 403);

        $categories->delete($id);
        session()->flash('status', __('Category archived.'));
    }

    public function render(CategoryRepository $categories): View
    {
        return view('livewire.categories.index', [
            'categories' => $categories->paginateFiltered($this->search),
        ]);
    }
}
