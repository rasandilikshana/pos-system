<div class="space-y-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('categories.index')">{{ __('Categories') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $category ? __('Edit') : __('New') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl">{{ $category ? __('Edit category') : __('New category') }}</flux:heading>

    <form wire:submit="save">
        <flux:card class="space-y-4">
            <flux:input wire:model="form.name" :label="__('Name')" required />
            <flux:input wire:model="form.slug" :label="__('Slug')" :description="__('Lowercase URL slug. Leave blank to auto-generate from name.')" />
            <flux:textarea wire:model="form.description" :label="__('Description')" rows="3" />
            <flux:switch wire:model="form.is_active" :label="__('Active')" />
        </flux:card>

        <div class="mt-6 flex justify-end gap-3">
            <flux:button :href="route('categories.index')" variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
        </div>
    </form>
</div>
