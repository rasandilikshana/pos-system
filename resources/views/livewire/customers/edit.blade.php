<div class="space-y-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('customers.index')">{{ __('Customers') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $customer ? __('Edit') : __('New') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl">{{ $customer ? __('Edit customer') : __('New customer') }}</flux:heading>

    <form wire:submit="save">
        <flux:card class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model="form.name" :label="__('Name')" required />
                <flux:input wire:model="form.email" type="email" :label="__('Email')" />
                <flux:input wire:model="form.phone" :label="__('Phone')" />
            </div>
            <flux:textarea wire:model="form.address" :label="__('Address')" rows="2" />
            <flux:switch wire:model="form.is_active" :label="__('Active')" />
        </flux:card>

        <div class="mt-6 flex justify-end gap-3">
            <flux:button :href="route('customers.index')" variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
        </div>
    </form>
</div>
