<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
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

    public function delete(int $id): void
    {
        abort_unless(auth()->user()?->can('suppliers.manage'), 403);
        Supplier::findOrFail($id)->delete();
        session()->flash('status', __('Supplier archived.'));
    }

    public function render(): View
    {
        $suppliers = Supplier::query()
            ->when($this->search !== '', function ($q) {
                $like = '%'.$this->search.'%';
                $q->where(fn ($qq) => $qq->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like));
            })
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.suppliers.index', ['suppliers' => $suppliers]);
    }
}
