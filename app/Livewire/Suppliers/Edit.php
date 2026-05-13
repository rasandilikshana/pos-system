<?php

namespace App\Livewire\Suppliers;

use App\Actions\Suppliers\SaveSupplierAction;
use App\Livewire\Forms\SupplierForm;
use App\Models\Supplier;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Edit Supplier')]
class Edit extends Component
{
    public SupplierForm $form;

    public ?Supplier $supplier = null;

    public function mount(?Supplier $supplier = null): void
    {
        abort_unless(auth()->user()?->can('suppliers.manage'), 403);

        if ($supplier?->exists) {
            $this->supplier = $supplier;
            $this->form->setSupplier($supplier);
        }
    }

    public function save(SaveSupplierAction $save): mixed
    {
        $save->execute($this->form->attributes(), $this->supplier?->id);

        session()->flash('status', $this->supplier ? __('Supplier updated.') : __('Supplier created.'));

        return redirect()->route('suppliers.index');
    }

    public function render(): View
    {
        return view('livewire.suppliers.edit');
    }
}
