@php
$colorClasses = [
    'surname' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'name' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
    'date_gender' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
    'birthplace' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
    'checksum' => 'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200',
];
@endphp

<div class="flex flex-wrap items-center gap-3" data-codice-fiscale-breakdown>
    <div class="flex overflow-hidden rounded-md border border-zinc-200 font-mono text-lg tracking-wider dark:border-zinc-700">
        @foreach ($segments as $segment)
            <flux:tooltip content="{{ __('messages.breakdown.'.$segment['key']) }}">
                <span class="px-1.5 py-1 {{ $colorClasses[$segment['key']] }}">{{ $segment['value'] }}</span>
            </flux:tooltip>
        @endforeach
    </div>

    <flux:button
        size="sm"
        variant="ghost"
        type="button"
        x-data="{ copied: false }"
        x-on:click="navigator.clipboard.writeText(@js($code)); copied = true; setTimeout(() => copied = false, 1500)"
    >
        <span x-show="!copied">{{ __('messages.breakdown.copy') }}</span>
        <span x-show="copied" x-cloak>{{ __('messages.breakdown.copied') }}</span>
    </flux:button>
</div>
