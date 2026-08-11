@extends('layouts.app')

@section('content')
    <flux:heading size="xl">{{ $person->first_name }} {{ $person->last_name }}</flux:heading>

    <div class="mt-6 max-w-lg space-y-6">
        <flux:heading size="sm">{{ __('messages.people.show.reparse_heading') }}</flux:heading>
        <flux:text>{{ __('messages.people.show.reparse_tagline') }}</flux:text>

        <x-codice-fiscale-breakdown :code="$person->codice_fiscale->value()" />

        <dl class="divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
            <div class="flex justify-between gap-4 py-2">
                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.decode.result.gender') }}</dt>
                <dd>{{ $parsed->gender()->value === 'M' ? __('messages.decode.result.gender_male') : __('messages.decode.result.gender_female') }}</dd>
            </div>
            <div class="flex justify-between gap-4 py-2">
                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.decode.result.birth_date') }}</dt>
                <dd>{{ $parsed->birthDate()?->format('Y-m-d') }}</dd>
            </div>
            <div class="flex justify-between gap-4 py-2">
                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('messages.decode.result.birth_place') }}</dt>
                <dd>{{ $parsed->birthPlace()?->name() ?? __('messages.decode.result.birth_place_unknown') }}</dd>
            </div>
        </dl>

        <flux:button href="{{ route('people.index') }}" variant="ghost">
            &larr; {{ __('messages.people.back_to_list') }}
        </flux:button>
    </div>
@endsection
