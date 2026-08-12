@extends('layouts.app')

@section('content')
    <x-feature-page current="omocodia">
        <flux:heading size="xl">{{ __('messages.omocodia.heading') }}</flux:heading>
        <flux:text class="mt-2 max-w-2xl font-serif">{{ __('messages.omocodia.tagline') }}</flux:text>

        <livewire:explore.omocodia-playground />
    </x-feature-page>
@endsection
