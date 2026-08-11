@extends('layouts.app')

@section('content')
    <flux:heading size="xl">{{ __('messages.repository_swap.heading') }}</flux:heading>
    <flux:text class="mt-2 max-w-2xl">{{ __('messages.repository_swap.tagline') }}</flux:text>

    <livewire:explore.repository-swap-demo />
@endsection
