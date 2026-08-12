@extends('layouts.app')

@section('content')
    <x-feature-page current="birthplace_history">
        <flux:heading size="xl">{{ __('messages.birthplace_history.heading') }}</flux:heading>
        <flux:text class="mt-2 max-w-2xl font-serif">{{ __('messages.birthplace_history.tagline') }}</flux:text>

        <livewire:explore.birthplace-history-demo />
    </x-feature-page>
@endsection
