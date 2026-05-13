<?php

namespace App\Livewire\Users;

use App\Livewire\Forms\UserForm;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Edit User')]
class Edit extends Component
{
    public UserForm $form;

    public ?User $user = null;

    public function mount(?User $user = null): void
    {
        abort_unless(auth()->user()?->can('users.manage'), 403);

        if ($user?->exists) {
            $this->user = $user;
            $this->form->setUser($user);
        }
    }

    public function save(): mixed
    {
        $this->form->save();
        session()->flash('status', $this->user ? __('User updated.') : __('User created.'));

        return redirect()->route('users.index');
    }

    public function render(): View
    {
        return view('livewire.users.edit');
    }
}
