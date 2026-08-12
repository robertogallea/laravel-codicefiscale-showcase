@extends('layouts.app')

@section('content')
    <x-feature-page current="people">
        <flux:heading size="xl">{{ __('messages.people.create.heading') }}</flux:heading>
        <flux:text class="mt-2 max-w-2xl font-serif">{{ __('messages.people.create.tagline') }}</flux:text>

        <livewire:people.people-create />
    </x-feature-page>
@endsection
