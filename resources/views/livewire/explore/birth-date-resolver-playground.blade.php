<div class="mt-6 max-w-2xl space-y-6">
    <form wire:submit="resolve" class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <flux:input
            type="number"
            min="1"
            max="12"
            wire:model.live.debounce.400ms="month"
            label="{{ __('messages.birth_date_resolver.month_label') }}"
        />

        <flux:input
            type="number"
            min="1"
            max="31"
            wire:model.live.debounce.400ms="day"
            label="{{ __('messages.birth_date_resolver.day_label') }}"
        />

        <flux:input
            wire:model.live.debounce.400ms="twoDigitYear"
            maxlength="2"
            label="{{ __('messages.birth_date_resolver.two_digit_year_label') }}"
        />

        <flux:input
            type="number"
            min="0"
            wire:model.live.debounce.400ms="maxAge"
            label="{{ __('messages.birth_date_resolver.max_age_label') }}"
        />
    </form>

    <flux:select
        wire:model.live="birthPlaceCode"
        variant="combobox"
        :filter="false"
        label="{{ __('messages.hero.fields.birth_place') }}"
    >
        <x-slot name="input">
            <flux:select.input
                wire:model.live="birthPlaceQuery"
                placeholder="{{ __('messages.hero.fields.birth_place_placeholder') }}"
            />
        </x-slot>

        @foreach ($this->birthPlaceOptions as $option)
            <flux:select.option value="{{ $option['code'] }}" wire:key="birth-date-resolver-place-{{ $option['code'] }}">
                {{ $option['label'] }}
            </flux:select.option>
        @endforeach
    </flux:select>

    <dl class="divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
        <div class="flex justify-between gap-4 py-2">
            <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.birth_date_resolver.possible_years_label') }}</dt>
            <dd class="font-mono">{{ implode(', ', $possibleYears ?? []) }}</dd>
        </div>
        <div class="flex justify-between gap-4 py-2">
            <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.birth_date_resolver.resolved_date_label') }}</dt>
            <dd class="font-mono">{{ $resolvedDate ?? __('messages.birth_date_resolver.no_plausible_date') }}</dd>
        </div>
    </dl>

    <flux:text size="sm" variant="subtle">{{ __('messages.birth_date_resolver.tiebreak_note') }}</flux:text>
</div>
