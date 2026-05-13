<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <flux:heading size="xl">{{ __('Suppliers') }}</flux:heading>
        @can('suppliers.manage')
            <flux:button :href="route('suppliers.create')" variant="primary" icon="plus">{{ __('New supplier') }}</flux:button>
        @endcan
    </div>

    <flux:card>
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search suppliers...') }}" icon="magnifying-glass" />
    </flux:card>

    <flux:table :paginate="$suppliers">
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Contact') }}</flux:table.column>
            <flux:table.column>{{ __('Email') }}</flux:table.column>
            <flux:table.column>{{ __('Phone') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($suppliers as $supplier)
                <flux:table.row>
                    <flux:table.cell>{{ $supplier->name }}</flux:table.cell>
                    <flux:table.cell>{{ $supplier->contact_name ?? '—' }}</flux:table.cell>
                    <flux:table.cell class="text-sm">{{ $supplier->email ?? '—' }}</flux:table.cell>
                    <flux:table.cell class="text-sm">{{ $supplier->phone ?? '—' }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($supplier->is_active)
                            <flux:badge color="green" size="sm">{{ __('Active') }}</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">{{ __('Archived') }}</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                            <flux:menu>
                                @can('suppliers.manage')
                                    <flux:menu.item :href="route('suppliers.edit', $supplier)" icon="pencil-square">{{ __('Edit') }}</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item wire:click="delete({{ $supplier->id }})" wire:confirm="{{ __('Archive this supplier?') }}" variant="danger" icon="trash">{{ __('Archive') }}</flux:menu.item>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="6" class="text-center text-zinc-500">{{ __('No suppliers found.') }}</flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
