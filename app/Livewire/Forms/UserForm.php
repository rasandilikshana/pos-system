<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

/** Form Object for create/edit User — validation + data shaping only. Persistence lives in SaveUserAction. */
class UserForm extends Form
{
    public ?int $id = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    public string $email = '';

    public ?string $password = null;

    #[Validate('required|in:admin,manager,cashier')]
    public string $role = 'cashier';

    public bool $is_active = true;

    public function setUser(?User $user): void
    {
        if (! $user) {
            return;
        }

        $this->fill([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->getRoleNames()->first() ?? 'cashier',
            'is_active' => (bool) $user->is_active,
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->id)->whereNull('deleted_at'),
            ],
            'password' => $this->id
                ? ['nullable', 'string', 'min:8']
                : ['required', 'string', 'min:8'],
        ];
    }

    /** @return array{name:string,email:string,role:string,is_active:bool,password:?string} */
    public function attributes(): array
    {
        $this->validate();

        return [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active,
            'password' => $this->password,
        ];
    }
}
