<?php

namespace App\Livewire\Forms;

use App\Models\Supplier;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SupplierForm extends Form
{
    public ?int $id = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:255')]
    public ?string $contact_name = null;

    public ?string $email = null;

    #[Validate('nullable|string|max:32')]
    public ?string $phone = null;

    #[Validate('nullable|string|max:1000')]
    public ?string $address = null;

    public bool $is_active = true;

    public function setSupplier(?Supplier $supplier): void
    {
        if (! $supplier) {
            return;
        }

        $this->fill([
            'id' => $supplier->id,
            'name' => $supplier->name,
            'contact_name' => $supplier->contact_name,
            'email' => $supplier->email,
            'phone' => $supplier->phone,
            'address' => $supplier->address,
            'is_active' => (bool) $supplier->is_active,
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('suppliers', 'email')->ignore($this->id)->whereNull('deleted_at'),
            ],
        ];
    }

    public function save(): Supplier
    {
        $this->validate();

        $attrs = $this->except('id');

        if ($this->id) {
            $supplier = Supplier::findOrFail($this->id);
            $supplier->update($attrs);

            return $supplier->fresh();
        }

        return Supplier::create($attrs);
    }
}
