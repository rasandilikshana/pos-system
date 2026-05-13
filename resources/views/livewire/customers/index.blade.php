<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <flux:heading size="xl">{{ __('Customers') }}</flux:heading>
        @can('customers.manage')
            <flux:button :href="route('customers.create')" variant="primary" icon="plus">{{ __('New customer') }}</flux:button>
        @endcan
    </div>

    <flux:card>
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search customers...') }}" icon="magnifying-glass" />
    </flux:card>

    <flux:table :paginate="$customers">
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Email') }}</flux:table.column>
            <flux:table.column>{{ __('Phone') }}</flux:table.column>
            <flux:table.column align="right">{{ __('Points') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($customers as $customer)
                <flux:table.row>
                    <flux:table.cell>{{ $customer->name }}</flux:table.cell>
                    <flux:table.cell class="text-sm">{{ $customer->email ?? '—' }}</flux:table.cell>
                    <flux:table.cell class="text-sm">{{ $customer->phone ?? '—' }}</flux:table.cell>
                    <flux:table.cell align="right">{{ $customer->loyalty_points }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($customer->is_active)
                            <flux:badge color="green" size="sm">{{ __('Active') }}</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">{{ __('Archived') }}</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                            <flux:menu>
                                @can('customers.manage')
                                    <flux:menu.item :href="route('customers.edit', $customer)" icon="pencil-square">{{ __('Edit') }}</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item wire:click="delete({{ $customer->id }})" wire:confirm="{{ __('Archive this customer?') }}" variant="danger" icon="trash">{{ __('Archive') }}</flux:menu.item>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="6" class="text-center text-zinc-500">{{ __('No customers found.') }}</flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
