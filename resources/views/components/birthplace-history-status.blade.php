@props(['result'])

@switch($result['status'])
    @case('valid')
        <dl class="divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
            <div class="flex justify-between gap-4 py-2">
                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.birthplace_history.name_label') }}</dt>
                <dd class="font-mono">{{ $result['name'] }}</dd>
            </div>
            <div class="flex justify-between gap-4 py-2">
                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.birthplace_history.province_label') }}</dt>
                <dd class="font-mono">{{ $result['province'] }}</dd>
            </div>
        </dl>
        @break

    @case('not_valid_on_date')
        <flux:badge color="amber" icon="exclamation-triangle">
            {{ __('messages.birthplace_history.not_valid_on_date') }}
        </flux:badge>
        @break

    @case('never_existed')
        <flux:badge color="zinc" icon="minus">
            {{ __('messages.birthplace_history.never_existed') }}
        </flux:badge>
        @break

    @case('no_date')
        <flux:text size="sm" variant="subtle">{{ __('messages.birthplace_history.no_date') }}</flux:text>
        @break
@endswitch
