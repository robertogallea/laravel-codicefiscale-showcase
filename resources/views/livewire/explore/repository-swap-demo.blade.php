<div class="mt-6 max-w-2xl space-y-6">
    <flux:callout icon="information-circle">
        <flux:callout.text>{{ __('messages.repository_swap.demo_notice') }}</flux:callout.text>
    </flux:callout>

    <div class="text-sm">
        <span class="text-zinc-500 dark:text-zinc-400">{{ __('messages.repository_swap.code_label') }}:</span>
        <span class="font-mono">{{ $code }}</span>
    </div>

    <flux:radio.group
        wire:model.live="repository"
        label="{{ __('messages.repository_swap.repository_label') }}"
        variant="segmented"
    >
        <flux:radio value="real" label="{{ __('messages.repository_swap.real_label') }}" />
        <flux:radio value="demo" label="{{ __('messages.repository_swap.demo_label') }}" />
    </flux:radio.group>

    @if ($valid === true)
        <div class="space-y-4">
            <x-codice-fiscale-breakdown :code="$code" />

            <dl class="max-w-lg divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                <div class="flex justify-between gap-4 py-2">
                    <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.decode.result.gender') }}</dt>
                    <dd>{{ $details['gender'] === 'M' ? __('messages.decode.result.gender_male') : __('messages.decode.result.gender_female') }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2">
                    <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.decode.result.birth_date') }}</dt>
                    <dd>{{ $details['birth_date'] }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2">
                    <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.decode.result.birth_place') }}</dt>
                    <dd>{{ $details['birth_place'] ?? __('messages.decode.result.birth_place_unknown') }}</dd>
                </div>
            </dl>
        </div>
    @elseif ($valid === false)
        <flux:callout variant="danger" icon="exclamation-triangle">
            <flux:callout.text>{{ $errorMessage }}</flux:callout.text>
        </flux:callout>
    @endif
</div>
