@extends('layouts.app')

@section('content')
    <x-feature-page current="repository_swap">
        <flux:heading size="xl">{{ __('messages.repository_swap.heading') }}</flux:heading>
        <flux:text class="mt-2 max-w-2xl font-serif">{{ __('messages.repository_swap.tagline') }}</flux:text>

        <livewire:explore.repository-swap-demo />
    </x-feature-page>
@endsection
