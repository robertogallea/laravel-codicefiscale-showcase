@extends('layouts.app')

@section('content')
    <flux:heading size="xl">{{ __('messages.birth_date_resolver.heading') }}</flux:heading>
    <flux:text class="mt-2 max-w-2xl">{{ __('messages.birth_date_resolver.tagline') }}</flux:text>

    <livewire:explore.birth-date-resolver-playground />
@endsection
