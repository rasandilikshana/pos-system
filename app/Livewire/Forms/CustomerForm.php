<?php

namespace App\Livewire\Forms;

use App\Models\Customer;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

/** Form Object for create/edit Customer — validation + data shaping only. Persistence lives in SaveCustomerAction. */
class CustomerForm extends Form
{
    public ?int $id = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    public ?string $email = null;

    #[Validate('nullable|string|max:32')]
    public ?string $phone = null;

    #[Validate('nullable|string|max:1000')]
    public ?string $address = null;

    public bool $is_active = true;

    public function setCustomer(?Customer $customer): void
    {
        if (! $customer) {
            return;
        }

        $this->fill([
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'is_active' => (bool) $customer->is_active,
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('customers', 'email')->ignore($this->id)->whereNull('deleted_at'),
            ],
        ];
    }

    /** @return array<string, mixed> */
    public function attributes(): array
    {
        $this->validate();

        return $this->except('id');
    }
}
