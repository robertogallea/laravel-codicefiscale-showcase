@props(['current'])

@php
    $neighbors = \App\Support\FeatureCatalog::neighbors($current);
    $entry = \App\Support\FeatureCatalog::find($current);
@endphp

<div {{ $attributes }}>
    <div class="flex items-baseline gap-2 font-mono text-xs uppercase tracking-[0.2em] text-salvia dark:text-salvia-bright">
        <span>{{ __('messages.nav.quadro', ['code' => $entry['quadro']]) }}</span>
        <span aria-hidden="true">&middot;</span>
        <span>{{ __('messages.nav.position', ['position' => $neighbors['position'], 'total' => $neighbors['total']]) }}</span>
    </div>

    <div class="mt-4">
        {{ $slot }}
    </div>

    <nav aria-label="{{ __('messages.nav.label') }}" class="mt-16 flex items-stretch justify-between gap-4 border-t border-filigrana pt-6 dark:border-filigrana-night">
        <a href="{{ $neighbors['prev']['url'] }}" class="group flex-1">
            <span class="block font-mono text-[10px] uppercase tracking-[0.2em] text-salvia dark:text-salvia-bright">&larr; {{ __('messages.nav.prev') }}</span>
            <span class="mt-1 block font-medium text-inchiostro group-hover:text-bosco dark:text-avorio dark:group-hover:text-salvia-bright">{{ __($neighbors['prev']['headingKey']) }}</span>
        </a>

        <a
            href="{{ route('home') }}"
            class="hidden flex-none items-center px-4 font-mono text-xs uppercase tracking-[0.2em] text-salvia hover:text-bosco sm:flex dark:text-salvia-bright dark:hover:text-avorio"
        >
            {{ __('messages.nav.back_to_hero') }}
        </a>

        <a href="{{ $neighbors['next']['url'] }}" class="group flex-1 text-right">
            <span class="block font-mono text-[10px] uppercase tracking-[0.2em] text-salvia dark:text-salvia-bright">{{ __('messages.nav.next') }} &rarr;</span>
            <span class="mt-1 block font-medium text-inchiostro group-hover:text-bosco dark:text-avorio dark:group-hover:text-salvia-bright">{{ __($neighbors['next']['headingKey']) }}</span>
        </a>
    </nav>
</div>
