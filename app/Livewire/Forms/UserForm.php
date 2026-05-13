<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

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

    public function save(): User
    {
        $this->validate();

        $attrs = [
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $attrs['password'] = Hash::make($this->password);
        }

        if ($this->id) {
            $user = User::findOrFail($this->id);
            $user->update($attrs);
        } else {
            $attrs['email_verified_at'] = now();
            $user = User::create($attrs);
        }

        $user->syncRoles([$this->role]);

        return $user->fresh();
    }
}
