<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <flux:heading size="xl">{{ __('Staff') }}</flux:heading>
        <flux:button :href="route('users.create')" variant="primary" icon="plus">{{ __('New user') }}</flux:button>
    </div>

    <flux:card>
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search staff...') }}" icon="magnifying-glass" />
    </flux:card>

    <flux:table :paginate="$users">
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Email') }}</flux:table.column>
            <flux:table.column>{{ __('Role') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($users as $user)
                <flux:table.row>
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <flux:avatar circle :name="$user->name" class="!size-8" />
                            {{ $user->name }}
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="text-sm">{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        @foreach ($user->roles as $role)
                            <flux:badge color="zinc" size="sm">{{ $role->name }}</flux:badge>
                        @endforeach
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($user->is_active)
                            <flux:badge color="green" size="sm">{{ __('Active') }}</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">{{ __('Suspended') }}</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                            <flux:menu>
                                <flux:menu.item :href="route('users.edit', $user)" icon="pencil-square">{{ __('Edit') }}</flux:menu.item>
                                @if ($user->id !== auth()->id())
                                    <flux:menu.separator />
                                    <flux:menu.item wire:click="delete({{ $user->id }})" wire:confirm="{{ __('Suspend this user?') }}" variant="danger" icon="trash">{{ __('Suspend') }}</flux:menu.item>
                                @endif
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="5" class="text-center text-zinc-500">{{ __('No users found.') }}</flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
