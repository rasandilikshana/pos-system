<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <flux:heading size="xl">{{ __('Categories') }}</flux:heading>
        @can('categories.manage')
            <flux:button :href="route('categories.create')" variant="primary" icon="plus">{{ __('New category') }}</flux:button>
        @endcan
    </div>

    <flux:card>
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search categories...') }}" icon="magnifying-glass" />
    </flux:card>

    <flux:table :paginate="$categories">
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Slug') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($categories as $category)
                <flux:table.row>
                    <flux:table.cell>{{ $category->name }}</flux:table.cell>
                    <flux:table.cell class="font-mono text-xs">{{ $category->slug }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($category->is_active)
                            <flux:badge color="green" size="sm">{{ __('Active') }}</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">{{ __('Archived') }}</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                            <flux:menu>
                                @can('categories.manage')
                                    <flux:menu.item :href="route('categories.edit', $category)" icon="pencil-square">{{ __('Edit') }}</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item wire:click="delete({{ $category->id }})" wire:confirm="{{ __('Archive this category?') }}" variant="danger" icon="trash">{{ __('Archive') }}</flux:menu.item>
                                @endcan
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="4" class="text-center text-zinc-500">{{ __('No categories found.') }}</flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
