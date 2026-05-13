<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <flux:heading size="xl">{{ __('Products') }}</flux:heading>

        @can('products.manage')
            <flux:button :href="route('products.create')" variant="primary" icon="plus">
                {{ __('New product') }}
            </flux:button>
        @endcan
    </div>

    <flux:card>
        <div class="flex flex-wrap items-end gap-3">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Search by name, SKU, or barcode...') }}"
                icon="magnifying-glass"
                class="min-w-64"
            />

            <flux:select wire:model.live="category" placeholder="{{ __('All categories') }}">
                <flux:select.option value="">{{ __('All categories') }}</flux:select.option>
                @foreach ($categories as $c)
                    <flux:select.option :value="$c->id">{{ $c->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </flux:card>

    <flux:table :paginate="$products">
        <flux:table.columns>
            <flux:table.column>{{ __('SKU') }}</flux:table.column>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Category') }}</flux:table.column>
            <flux:table.column align="right">{{ __('Unit price') }}</flux:table.column>
            <flux:table.column align="right">{{ __('Stock') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($products as $product)
                <flux:table.row>
                    <flux:table.cell class="font-mono text-xs">{{ $product->sku }}</flux:table.cell>
                    <flux:table.cell>{{ $product->name }}</flux:table.cell>
                    <flux:table.cell>{{ $product->category?->name ?? '—' }}</flux:table.cell>
                    <flux:table.cell align="right">{{ number_format((float) $product->unit_price, 2) }}</flux:table.cell>
                    <flux:table.cell align="right">
                        @if ($product->current_stock <= $product->reorder_level)
                            <flux:badge color="amber" size="sm">{{ $product->current_stock }}</flux:badge>
                        @else
                            {{ $product->current_stock }}
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($product->is_active)
                            <flux:badge color="green" size="sm">{{ __('Active') }}</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">{{ __('Archived') }}</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                            <flux:menu>
                                @can('products.manage')
                                    <flux:menu.item :href="route('products.edit', $product)" icon="pencil-square">
                                        {{ __('Edit') }}
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item
                                        wire:click="delete({{ $product->id }})"
                                        wire:confirm="{{ __('Archive this product?') }}"
                                        variant="danger"
                                        icon="trash"
                                    >
                                        {{ __('Archive') }}
                                    </flux:menu.item>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center text-zinc-500">
                        {{ __('No products found.') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
