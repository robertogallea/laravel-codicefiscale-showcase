@extends('layouts.app')

@section('content')
    <div class="grid gap-8 lg:grid-cols-2 lg:items-start">
        <div>
            <flux:heading size="xl">{{ config('app.name') }}</flux:heading>
            <flux:text class="mt-2">{{ __('messages.home.tagline') }}</flux:text>

            <livewire:hero-tool />
        </div>

        <div>
            <flux:heading size="lg">{{ __('messages.home.features_heading') }}</flux:heading>

            <div class="mt-4 grid gap-4">
                @foreach ($features as $feature)
                    <a href="{{ $feature['route'] }}" class="block transition hover:border-zinc-300 dark:hover:border-zinc-500">
                        <flux:card size="sm">
                            <flux:heading size="sm">{{ $feature['heading'] }}</flux:heading>
                            <flux:text class="mt-1 text-sm">{{ $feature['description'] }}</flux:text>
                        </flux:card>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
