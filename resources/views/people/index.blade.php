@extends('layouts.app')

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('messages.people.heading') }}</flux:heading>
            <flux:text class="mt-2 max-w-2xl">{{ __('messages.people.tagline') }}</flux:text>
        </div>

        <flux:button href="{{ route('people.create') }}" variant="primary">
            {{ __('messages.people.new') }}
        </flux:button>
    </div>

    <livewire:people.people-index />
@endsection
