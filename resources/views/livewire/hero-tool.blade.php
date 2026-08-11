<div>
    <flux:tab.group>
        <flux:tabs wire:model="mode">
            <flux:tab name="calculate">{{ __('messages.hero.calculate.tab') }}</flux:tab>
            <flux:tab name="decode">{{ __('messages.hero.decode.tab') }}</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="calculate">
            <form wire:submit="generate" class="mt-6 grid max-w-lg gap-4">
                <flux:input
                    wire:model="firstName"
                    label="{{ __('messages.hero.calculate.first_name') }}"
                />

                <flux:input
                    wire:model="lastName"
                    label="{{ __('messages.hero.calculate.last_name') }}"
                />

                <flux:input
                    type="date"
                    wire:model="birthDate"
                    label="{{ __('messages.hero.calculate.birth_date') }}"
                />

                <flux:radio.group wire:model="gender" label="{{ __('messages.hero.calculate.gender') }}" variant="segmented">
                    <flux:radio value="M" label="{{ __('messages.hero.calculate.gender_male') }}" />
                    <flux:radio value="F" label="{{ __('messages.hero.calculate.gender_female') }}" />
                </flux:radio.group>

                <flux:select
                    wire:model="birthPlaceCode"
                    variant="combobox"
                    :filter="false"
                    label="{{ __('messages.hero.calculate.birth_place') }}"
                >
                    <x-slot name="input">
                        <flux:select.input
                            wire:model.live="birthPlaceQuery"
                            placeholder="{{ __('messages.hero.calculate.birth_place_placeholder') }}"
                        />
                    </x-slot>

                    @foreach ($this->birthPlaceOptions as $option)
                        <flux:select.option value="{{ $option['code'] }}" wire:key="birth-place-{{ $option['code'] }}">
                            {{ $option['label'] }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <div>
                    <flux:button type="submit" variant="primary">
                        {{ __('messages.hero.calculate.submit') }}
                    </flux:button>
                </div>
            </form>

            @if ($generatedCode)
                <div class="mt-6 space-y-3">
                    <x-codice-fiscale-breakdown :code="$generatedCode" />

                    <flux:button size="sm" variant="ghost" wire:click="verifyGeneratedCode">
                        {{ __('messages.hero.calculate.verify') }} &rarr;
                    </flux:button>
                </div>
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="decode">
            <form wire:submit="decode" class="mt-6 grid max-w-lg gap-4">
                <flux:input
                    wire:model="decodeInput"
                    label="{{ __('messages.hero.decode.input') }}"
                    placeholder="{{ __('messages.hero.decode.input_placeholder') }}"
                />

                <div>
                    <flux:button type="submit" variant="primary">
                        {{ __('messages.hero.decode.submit') }}
                    </flux:button>
                </div>
            </form>

            @if ($decodeValid === true)
                <div class="mt-6 space-y-4">
                    <x-codice-fiscale-breakdown :code="$decodedCode" />

                    <dl class="max-w-lg divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                        <div class="flex justify-between gap-4 py-2">
                            <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.decode.result.gender') }}</dt>
                            <dd>{{ $decodedDetails['gender'] === 'M' ? __('messages.decode.result.gender_male') : __('messages.decode.result.gender_female') }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-2">
                            <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.decode.result.birth_date') }}</dt>
                            <dd>{{ $decodedDetails['birth_date'] }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-2">
                            <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.decode.result.birth_place') }}</dt>
                            <dd>{{ $decodedDetails['birth_place'] ?? __('messages.decode.result.birth_place_unknown') }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-2">
                            <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.decode.result.omocodia') }}</dt>
                            <dd>{{ $decodedDetails['is_omocodia'] ? __('messages.decode.result.yes') : __('messages.decode.result.no') }}</dd>
                        </div>
                    </dl>
                </div>
            @elseif ($decodeValid === false)
                <flux:callout variant="danger" class="mt-6 max-w-lg" icon="exclamation-triangle">
                    <flux:callout.text>{{ $decodeErrorMessage }}</flux:callout.text>
                </flux:callout>
            @endif
        </flux:tab.panel>
    </flux:tab.group>
</div>
