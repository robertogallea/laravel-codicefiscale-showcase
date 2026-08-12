@extends('layouts.app')

@section('content')
    <x-feature-page current="people">
        <div class="flex items-start justify-between gap-4">
            <div>
                <flux:heading size="xl">{{ __('messages.people.heading') }}</flux:heading>
                <flux:text class="mt-2 max-w-2xl font-serif">{{ __('messages.people.tagline') }}</flux:text>
            </div>

            <flux:button href="{{ route('people.create') }}" variant="primary">
                {{ __('messages.people.new') }}
            </flux:button>
        </div>

        <livewire:people.people-index />
    </x-feature-page>
@endsection
