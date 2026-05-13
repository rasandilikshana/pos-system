<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
        <flux:subheading>{{ __('Welcome back,') }} {{ auth()->user()->name }}</flux:subheading>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <flux:card>
            <flux:subheading>{{ __('Active products') }}</flux:subheading>
            <flux:heading size="xl" class="mt-1">{{ $productCount }}</flux:heading>
        </flux:card>

        <flux:card>
            <flux:subheading>{{ __('Low stock') }}</flux:subheading>
            <flux:heading size="xl" class="mt-1 text-amber-600 dark:text-amber-400">{{ $lowStockCount }}</flux:heading>
        </flux:card>

        <flux:card>
            <flux:subheading>{{ __('Customers') }}</flux:subheading>
            <flux:heading size="xl" class="mt-1">{{ $customerCount }}</flux:heading>
        </flux:card>

        <flux:card>
            <flux:subheading>{{ __("Today's sales") }}</flux:subheading>
            <flux:heading size="xl" class="mt-1">{{ number_format((float) $todaySales, 2) }}</flux:heading>
        </flux:card>
    </div>
</div>
