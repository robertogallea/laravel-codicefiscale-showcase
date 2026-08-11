@extends('layouts.app')

@section('content')
    <flux:heading size="xl">{{ config('app.name') }}</flux:heading>
    <flux:text class="mt-2">{{ __('messages.home.tagline') }}</flux:text>
@endsection
