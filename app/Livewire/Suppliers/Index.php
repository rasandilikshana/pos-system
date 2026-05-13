<?php

namespace App\Livewire\Suppliers;

use App\Repositories\SupplierRepository;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Suppliers')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('suppliers.view'), 403);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, SupplierRepository $suppliers): void
    {
        abort_unless(auth()->user()?->can('suppliers.manage'), 403);

        $suppliers->delete($id);
        session()->flash('status', __('Supplier archived.'));
    }

    public function render(SupplierRepository $suppliers): View
    {
        return view('livewire.suppliers.index', [
            'suppliers' => $suppliers->paginateFiltered($this->search),
        ]);
    }
}
