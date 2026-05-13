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
<body class="min-h-full bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-900 dark:text-white">

<main class="flex min-h-dvh items-center justify-center p-6">
    <div class="w-full max-w-md">
        <div class="mb-8 flex items-center justify-center gap-2">
            <div class="flex h-10 w-10 items-center justify-center rounded bg-zinc-900 text-white dark:bg-white dark:text-zinc-900">
                <flux:icon.shopping-cart variant="micro" />
            </div>
            <span class="text-lg font-semibold">{{ config('app.name') }}</span>
        </div>

        {{ $slot }}
    </div>
</main>

@fluxScripts
@livewireScripts
</body>
</html>
