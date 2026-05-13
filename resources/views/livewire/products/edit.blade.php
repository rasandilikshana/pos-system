<div class="space-y-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('products.index')">{{ __('Products') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $product ? __('Edit') : __('New') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl">{{ $product ? __('Edit product') : __('New product') }}</flux:heading>

    <form wire:submit="save" class="space-y-6">
        <flux:card class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model="form.sku" :label="__('SKU')" required />
                <flux:input wire:model="form.barcode" :label="__('Barcode')" />
                <flux:input wire:model="form.name" :label="__('Name')" required class="md:col-span-2" />

                <flux:select wire:model="form.category_id" :label="__('Category')" placeholder="{{ __('Choose...') }}">
                    @foreach ($categories as $c)
                        <flux:select.option :value="$c->id">{{ $c->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="form.supplier_id" :label="__('Supplier')" placeholder="{{ __('Choose...') }}">
                    <flux:select.option value="">{{ __('— None —') }}</flux:select.option>
                    @foreach ($suppliers as $s)
                        <flux:select.option :value="$s->id">{{ $s->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <flux:textarea wire:model="form.description" :label="__('Description')" rows="3" />
        </flux:card>

        <flux:card class="space-y-4">
            <flux:heading size="lg">{{ __('Pricing & stock') }}</flux:heading>
            <div class="grid gap-4 md:grid-cols-4">
                <flux:input wire:model="form.unit_price" type="number" step="0.01" :label="__('Unit price')" required />
                <flux:input wire:model="form.cost_price" type="number" step="0.01" :label="__('Cost price')" required />
                <flux:input wire:model="form.current_stock" type="number" :label="__('Current stock')" required />
                <flux:input wire:model="form.reorder_level" type="number" :label="__('Reorder level')" required />
            </div>

            <flux:switch wire:model="form.is_active" :label="__('Active')" />
        </flux:card>

        <div class="flex justify-end gap-3">
            <flux:button :href="route('products.index')" variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
        </div>
    </form>
</div>
