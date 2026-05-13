<div class="space-y-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('users.index')">{{ __('Staff') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $user ? __('Edit') : __('New') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl">{{ $user ? __('Edit user') : __('New user') }}</flux:heading>

    <form wire:submit="save">
        <flux:card class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model="form.name" :label="__('Name')" required />
                <flux:input wire:model="form.email" type="email" :label="__('Email')" required />
            </div>

            <flux:input
                wire:model="form.password"
                type="password"
                :label="__('Password')"
                :description="$user ? __('Leave blank to keep current password.') : __('Minimum 8 characters.')"
                viewable
            />

            <flux:select wire:model="form.role" :label="__('Role')">
                <flux:select.option value="admin">{{ __('Admin') }}</flux:select.option>
                <flux:select.option value="manager">{{ __('Manager') }}</flux:select.option>
                <flux:select.option value="cashier">{{ __('Cashier') }}</flux:select.option>
            </flux:select>

            <flux:switch wire:model="form.is_active" :label="__('Active')" />
        </flux:card>

        <div class="mt-6 flex justify-end gap-3">
            <flux:button :href="route('users.index')" variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
        </div>
    </form>
</div>
