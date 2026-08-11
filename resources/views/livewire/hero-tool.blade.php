<div>
    <flux:tab.group>
        <flux:tabs>
            <flux:tab name="calculate">{{ __('messages.hero.calculate.tab') }}</flux:tab>
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
                <div class="mt-6">
                    <x-codice-fiscale-breakdown :code="$generatedCode" />
                </div>
            @endif
        </flux:tab.panel>
    </flux:tab.group>
</div>
