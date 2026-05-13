<?php

namespace App\Livewire\Users;

use App\Repositories\UserRepository;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Staff')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('users.manage'), 403);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, UserRepository $users): void
    {
        abort_unless(auth()->user()?->can('users.manage'), 403);

        if ($id === auth()->id()) {
            session()->flash('status', __('You cannot delete your own account.'));

            return;
        }

        $users->delete($id);
        session()->flash('status', __('User suspended.'));
    }

    public function render(UserRepository $users): View
    {
        return view('livewire.users.index', [
            'users' => $users->paginateFiltered($this->search),
        ]);
    }
}
