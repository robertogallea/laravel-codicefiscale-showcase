<div class="mt-6 max-w-2xl space-y-8">
    <flux:input
        type="date"
        wire:model.live="date"
        label="{{ __('messages.birthplace_history.date_label') }}"
    />

    <div class="space-y-3">
        <flux:heading size="sm">{{ __('messages.birthplace_history.comune_section') }}</flux:heading>
        <x-birthplace-history-status :result="$historyResult" />
    </div>

    <div class="space-y-3">
        <flux:heading size="sm">{{ __('messages.birthplace_history.never_existed_section') }}</flux:heading>
        <x-birthplace-history-status :result="$neverExistedResult" />
    </div>
</div>
