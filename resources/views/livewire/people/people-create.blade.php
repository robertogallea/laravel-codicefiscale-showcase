<div>
    <form wire:submit="create" class="mt-6 grid max-w-lg gap-4">
        <flux:input
            wire:model="firstName"
            label="{{ __('messages.people.fields.first_name') }}"
        />

        <flux:input
            wire:model="lastName"
            label="{{ __('messages.people.fields.last_name') }}"
        />

        <flux:input
            type="date"
            wire:model="birthDate"
            label="{{ __('messages.people.fields.birth_date') }}"
        />

        <flux:radio.group wire:model="gender" label="{{ __('messages.people.fields.gender') }}" variant="segmented">
            <flux:radio value="M" label="{{ __('messages.people.fields.gender_male') }}" />
            <flux:radio value="F" label="{{ __('messages.people.fields.gender_female') }}" />
        </flux:radio.group>

        <flux:select
            wire:model="birthPlaceCode"
            variant="combobox"
            :filter="false"
            label="{{ __('messages.people.fields.birth_place') }}"
        >
            <x-slot name="input">
                <flux:select.input
                    wire:model.live="birthPlaceQuery"
                    placeholder="{{ __('messages.people.fields.birth_place_placeholder') }}"
                />
            </x-slot>

            @foreach ($this->birthPlaceOptions as $option)
                <flux:select.option value="{{ $option['code'] }}" wire:key="birth-place-{{ $option['code'] }}">
                    {{ $option['label'] }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:input
            wire:model="codiceFiscale"
            label="{{ __('messages.people.fields.codice_fiscale') }}"
            placeholder="{{ __('messages.people.fields.codice_fiscale_placeholder') }}"
        />

        <div>
            @error('creation')
                <p class="mb-3 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror

            <flux:button type="submit" variant="primary">
                {{ __('messages.people.create.submit') }}
            </flux:button>
        </div>
    </form>
</div>
