@extends('layouts.app')

@section('content')
    <div class="grid gap-12 lg:grid-cols-[minmax(0,3fr)_minmax(0,2fr)] lg:items-start">
        <div class="relative overflow-hidden rounded-2xl border border-filigrana bg-pergamena shadow-[0_18px_40px_-24px_rgba(18,59,44,0.45)] dark:border-filigrana-night dark:bg-pergamena-panel">
            <div class="flex items-center justify-between bg-bosco px-6 py-3 dark:bg-bosco-night">
                <span class="font-mono text-[11px] uppercase tracking-[0.3em] text-pergamena/80">{{ __('messages.home.card.eyebrow') }}</span>
                <span class="font-mono text-[11px] uppercase tracking-[0.3em] text-pergamena/80">{{ __('messages.home.card.edition', ['year' => now()->year]) }}</span>
            </div>

            <div class="px-6 py-8 sm:px-10 sm:py-10">
                <flux:heading size="xl">{{ config('app.name') }}</flux:heading>
                <flux:text class="mt-2 max-w-lg font-serif">{{ __('messages.home.tagline') }}</flux:text>

                <livewire:hero-tool />
            </div>
        </div>

        <div>
            <flux:heading size="lg">{{ __('messages.home.features_heading') }}</flux:heading>

            <ol class="mt-4 divide-y divide-filigrana border-y border-filigrana dark:divide-filigrana-night dark:border-filigrana-night">
                @foreach ($features as $feature)
                    <li>
                        <a
                            href="{{ $feature['route'] }}"
                            class="group flex items-start gap-4 py-4 transition hover:bg-filigrana/40 dark:hover:bg-pergamena-panel"
                        >
                            <span class="mt-1 shrink-0 font-mono text-xs uppercase tracking-[0.2em] text-salvia dark:text-salvia-bright">
                                {{ $feature['quadro'] }}
                            </span>

                            <span class="flex-1">
                                <flux:heading size="sm" class="group-hover:text-bosco dark:group-hover:text-salvia-bright">
                                    {{ $feature['heading'] }}
                                </flux:heading>
                                <flux:text class="mt-1 text-sm font-serif">{{ $feature['description'] }}</flux:text>
                            </span>

                            <span
                                aria-hidden="true"
                                class="mt-1 shrink-0 text-salvia opacity-0 transition group-hover:opacity-100 dark:text-salvia-bright"
                            >&rarr;</span>
                        </a>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
@endsection
