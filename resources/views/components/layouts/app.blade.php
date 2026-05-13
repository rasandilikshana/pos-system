<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="h-full bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-900 dark:text-white">

<flux:sidebar sticky stashable class="bg-zinc-50 border-r border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" inset="left" />

    <flux:brand href="{{ route('dashboard') }}" name="{{ config('app.name') }}" class="px-2">
        <x-slot name="logo">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-zinc-900 text-white dark:bg-white dark:text-zinc-900">
                <flux:icon.shopping-cart variant="micro" />
            </div>
        </x-slot>
    </flux:brand>

    <flux:navlist variant="outline">
        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
        </flux:navlist.item>

        <flux:navlist.group :heading="__('Catalog')" expandable>
            <flux:navlist.item icon="cube" :href="route('products.index')" :current="request()->routeIs('products.*')">
                {{ __('Products') }}
            </flux:navlist.item>
            <flux:navlist.item icon="folder" :href="route('categories.index')" :current="request()->routeIs('categories.*')">
                {{ __('Categories') }}
            </flux:navlist.item>
            <flux:navlist.item icon="truck" :href="route('suppliers.index')" :current="request()->routeIs('suppliers.*')">
                {{ __('Suppliers') }}
            </flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist.item icon="users" :href="route('customers.index')" :current="request()->routeIs('customers.*')">
            {{ __('Customers') }}
        </flux:navlist.item>

        @role('admin')
            <flux:navlist.item icon="shield-check" :href="route('users.index')" :current="request()->routeIs('users.*')">
                {{ __('Staff') }}
            </flux:navlist.item>
        @endrole
    </flux:navlist>

    <flux:spacer />

    <flux:dropdown position="top" align="start">
        <flux:profile :name="auth()->user()->name" :initials="auth()->user()->name[0] ?? '?'" />

        <flux:menu>
            <flux:menu.radio.group>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <flux:avatar circle :name="auth()->user()->name" class="!size-8 shrink-0" />
                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                </div>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                    {{ __('Log out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>

<flux:header sticky class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
    <flux:spacer />
</flux:header>

<flux:main>
    @if (session('status'))
        <flux:callout variant="success" :heading="session('status')" />
    @endif

    {{ $slot }}
</flux:main>

@fluxScripts
@livewireScripts
</body>
</html>
