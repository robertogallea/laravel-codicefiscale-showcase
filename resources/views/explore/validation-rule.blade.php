@extends('layouts.app')

@section('content')
    <flux:heading size="xl">{{ __('messages.validation_rule.heading') }}</flux:heading>
    <flux:text class="mt-2 max-w-2xl">{{ __('messages.validation_rule.tagline') }}</flux:text>

    <div class="mt-6 max-w-lg space-y-4">
        @if (session('validatedCode'))
            <flux:callout variant="success" icon="check-circle">
                <flux:callout.text>{{ __('messages.validation_rule.valid_message', ['code' => session('validatedCode')]) }}</flux:callout.text>
            </flux:callout>
        @endif

        <form method="POST" action="{{ route('explore.validation-rule.store') }}">
            @csrf

            <flux:input
                name="codice_fiscale"
                value="{{ old('codice_fiscale') }}"
                label="{{ __('messages.validation_rule.field_label') }}"
                placeholder="{{ __('messages.validation_rule.field_placeholder') }}"
            />

            <flux:button type="submit" variant="primary" class="mt-4">
                {{ __('messages.validation_rule.submit') }}
            </flux:button>
        </form>
    </div>
@endsection
