<?php

namespace App\Livewire\Customers;

use App\Livewire\Forms\CustomerForm;
use App\Models\Customer;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Edit Customer')]
class Edit extends Component
{
    public CustomerForm $form;

    public ?Customer $customer = null;

    public function mount(?Customer $customer = null): void
    {
        abort_unless(auth()->user()?->can('customers.manage'), 403);

        if ($customer?->exists) {
            $this->customer = $customer;
            $this->form->setCustomer($customer);
        }
    }

    public function save(): mixed
    {
        $this->form->save();
        session()->flash('status', $this->customer ? __('Customer updated.') : __('Customer created.'));

        return redirect()->route('customers.index');
    }

    public function render(): View
    {
        return view('livewire.customers.edit');
    }
}
