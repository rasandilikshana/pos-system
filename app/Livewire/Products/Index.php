<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Repositories\ProductRepository;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Products')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $category = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('products.view'), 403);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, ProductRepository $products): void
    {
        abort_unless(auth()->user()?->can('products.manage'), 403);

        $products->delete($id);
        session()->flash('status', __('Product archived.'));
    }

    public function render(ProductRepository $products): View
    {
        return view('livewire.products.index', [
            'products' => $products->paginateFiltered($this->search, $this->category !== '' ? (int) $this->category : null),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
