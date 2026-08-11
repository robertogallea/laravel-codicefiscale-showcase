<div class="mt-6 max-w-2xl space-y-6">
    <form wire:submit="loadCode" class="flex flex-wrap items-end gap-4">
        <div class="min-w-64 flex-1">
            <flux:input
                wire:model="code"
                label="{{ __('messages.omocodia.code_label') }}"
                autocomplete="off"
            />
        </div>

        <flux:button type="submit" variant="primary">
            {{ __('messages.omocodia.load') }}
        </flux:button>
    </form>

    @if ($error)
        <flux:callout variant="danger" icon="exclamation-triangle">
            <flux:callout.text>{{ $error }}</flux:callout.text>
        </flux:callout>
    @else
        <dl class="divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
            <div class="flex justify-between gap-4 py-2">
                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.omocodia.canonical_form') }}</dt>
                <dd class="font-mono">{{ $canonicalCode }}</dd>
            </div>
            <div class="flex justify-between gap-4 py-2">
                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.omocodia.level') }}</dt>
                <dd>{{ $level }} / 7</dd>
            </div>
        </dl>

        <div class="space-y-3">
            @foreach ($this->positions() as $index => $position)
                <flux:switch
                    :checked="$toggles[$index]"
                    wire:click="toggle({{ $index }})"
                    :label="$position['label']"
                    wire:key="omocodia-position-{{ $position['key'] }}"
                />
            @endforeach
        </div>

        <dl class="divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
            <div class="flex justify-between gap-4 py-2">
                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.omocodia.variant') }}</dt>
                <dd class="font-mono">{{ $variantCode }}</dd>
            </div>
            <div class="flex justify-between gap-4 py-2">
                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.omocodia.equivalent') }}</dt>
                <dd>
                    <flux:badge :color="$isEquivalent ? 'green' : 'red'" :icon="$isEquivalent ? 'check' : 'x-mark'">
                        {{ $isEquivalent ? __('messages.omocodia.yes') : __('messages.omocodia.no') }}
                    </flux:badge>
                </dd>
            </div>
        </dl>
    @endif
</div>
