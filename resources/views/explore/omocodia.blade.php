@extends('layouts.app')

@section('content')
    <flux:heading size="xl">{{ __('messages.omocodia.heading') }}</flux:heading>
    <flux:text class="mt-2 max-w-2xl">{{ __('messages.omocodia.tagline') }}</flux:text>

    <livewire:explore.omocodia-playground />
@endsection
