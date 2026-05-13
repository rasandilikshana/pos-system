<div class="space-y-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('suppliers.index')">{{ __('Suppliers') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $supplier ? __('Edit') : __('New') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl">{{ $supplier ? __('Edit supplier') : __('New supplier') }}</flux:heading>

    <form wire:submit="save">
        <flux:card class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model="form.name" :label="__('Name')" required />
                <flux:input wire:model="form.contact_name" :label="__('Contact name')" />
                <flux:input wire:model="form.email" type="email" :label="__('Email')" />
                <flux:input wire:model="form.phone" :label="__('Phone')" />
            </div>
            <flux:textarea wire:model="form.address" :label="__('Address')" rows="2" />
            <flux:switch wire:model="form.is_active" :label="__('Active')" />
        </flux:card>

        <div class="mt-6 flex justify-end gap-3">
            <flux:button :href="route('suppliers.index')" variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
        </div>
    </form>
</div>
