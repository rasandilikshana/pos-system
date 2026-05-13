<div>
    <flux:card>
        <div class="space-y-1">
            <flux:heading size="lg">{{ __('Sign in to your account') }}</flux:heading>
            <flux:subheading>{{ __('Use your staff credentials to continue.') }}</flux:subheading>
        </div>

        <form wire:submit="login" class="mt-6 space-y-6">
            <flux:input
                wire:model="form.email"
                type="email"
                :label="__('Email address')"
                required
                autocomplete="email"
                autofocus
            />

            <flux:input
                wire:model="form.password"
                type="password"
                :label="__('Password')"
                required
                viewable
                autocomplete="current-password"
            />

            <flux:checkbox wire:model="form.remember" :label="__('Remember me')" />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Sign in') }}
                </flux:button>
            </div>
        </form>
    </flux:card>

    <p class="mt-4 text-center text-xs text-zinc-500">
        {{ __('Dev: admin@pos.test / manager@pos.test / cashier@pos.test — pw: password') }}
    </p>
</div>
