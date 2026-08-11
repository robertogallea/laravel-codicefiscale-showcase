@extends('layouts.app')

@section('content')
    <flux:heading size="xl">{{ __('messages.people.create.heading') }}</flux:heading>
    <flux:text class="mt-2 max-w-2xl">{{ __('messages.people.create.tagline') }}</flux:text>

    <livewire:people.people-create />
@endsection
