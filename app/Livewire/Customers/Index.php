<?php

namespace App\Livewire\Customers;

use App\Repositories\CustomerRepository;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Customers')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('customers.view'), 403);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, CustomerRepository $customers): void
    {
        abort_unless(auth()->user()?->can('customers.manage'), 403);

        $customers->delete($id);
        session()->flash('status', __('Customer archived.'));
    }

    public function render(CustomerRepository $customers): View
    {
        return view('livewire.customers.index', [
            'customers' => $customers->paginateFiltered($this->search),
        ]);
    }
}
