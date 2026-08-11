@extends('layouts.app')

@section('content')
    <flux:heading size="xl">{{ __('messages.birthplace_history.heading') }}</flux:heading>
    <flux:text class="mt-2 max-w-2xl">{{ __('messages.birthplace_history.tagline') }}</flux:text>

    <livewire:explore.birthplace-history-demo />
@endsection
