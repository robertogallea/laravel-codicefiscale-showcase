@extends('layouts.app')

@section('content')
    <flux:heading size="xl">{{ __('messages.people.heading') }}</flux:heading>
    <flux:text class="mt-2 max-w-2xl">{{ __('messages.people.tagline') }}</flux:text>

    <livewire:people.people-index />
@endsection
