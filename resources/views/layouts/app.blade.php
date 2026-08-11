<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}@hasSection('title') &mdash; @yield('title')@endif</title>

    @fluxAppearance

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white text-zinc-800 dark:bg-zinc-900 dark:text-white">
    <header class="border-b border-zinc-200 dark:border-zinc-700">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
            <a href="{{ route('home') }}" class="text-lg font-semibold">
                {{ config('app.name') }}
            </a>

            <div class="flex items-center gap-4">
                <nav class="flex items-center gap-2 text-sm">
                    <a
                        href="{{ route('locale.switch', 'en') }}"
                        class="{{ app()->getLocale() === 'en' ? 'font-semibold' : 'text-zinc-500 dark:text-zinc-400' }}"
                    >EN</a>
                    <span class="text-zinc-300 dark:text-zinc-600">/</span>
                    <a
                        href="{{ route('locale.switch', 'it') }}"
                        class="{{ app()->getLocale() === 'it' ? 'font-semibold' : 'text-zinc-500 dark:text-zinc-400' }}"
                    >IT</a>
                </nav>

                <flux:dropdown>
                    <flux:button icon="swatch" size="sm" variant="ghost" aria-label="{{ __('messages.theme.label') }}" />

                    <flux:menu>
                        @foreach (['light' => 'sun', 'dark' => 'moon', 'system' => 'computer-desktop'] as $appearance => $icon)
                            <flux:menu.item icon="{{ $icon }}" onclick="window.Flux.appearance = '{{ $appearance }}'">
                                {{ __("messages.theme.{$appearance}") }}
                            </flux:menu.item>
                        @endforeach
                    </flux:menu>
                </flux:dropdown>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-10">
        @yield('content')
    </main>

    <footer class="mx-auto max-w-5xl px-6 pb-10">
        <x-birthplace-data-freshness />
    </footer>

    @fluxScripts
</body>
</html>
