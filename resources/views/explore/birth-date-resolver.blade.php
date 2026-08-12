@extends('layouts.app')

@section('content')
    <x-feature-page current="birth_date_resolver">
        <flux:heading size="xl">{{ __('messages.birth_date_resolver.heading') }}</flux:heading>
        <flux:text class="mt-2 max-w-2xl font-serif">{{ __('messages.birth_date_resolver.tagline') }}</flux:text>

        <livewire:explore.birth-date-resolver-playground />
    </x-feature-page>
@endsection
