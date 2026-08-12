<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}@hasSection('title') &mdash; @yield('title')@endif</title>

    @fluxAppearance

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-sfondo font-sans text-inchiostro dark:bg-bosco-night dark:text-avorio">
    <header class="border-b border-filigrana dark:border-filigrana-night">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
            <a
                href="{{ route('home') }}"
                class="flex items-center gap-3 text-lg font-semibold text-inchiostro dark:text-avorio"
                aria-label="{{ __('messages.nav.back_to_hero') }}"
            >
                <x-tessera-mark class="h-7 w-10 shrink-0" />
                {{ config('app.name') }}
            </a>

            <div class="flex items-center gap-4">
                <nav class="flex items-center gap-2 text-sm">
                    <a
                        href="{{ route('locale.switch', 'en') }}"
                        class="{{ app()->getLocale() === 'en' ? 'font-semibold' : 'text-salvia dark:text-salvia-bright' }}"
                    >EN</a>
                    <span class="text-filigrana dark:text-filigrana-night">/</span>
                    <a
                        href="{{ route('locale.switch', 'it') }}"
                        class="{{ app()->getLocale() === 'it' ? 'font-semibold' : 'text-salvia dark:text-salvia-bright' }}"
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
