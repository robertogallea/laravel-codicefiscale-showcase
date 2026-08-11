<div class="mt-6 max-w-3xl">
    @if ($people->isEmpty())
        <flux:text>{{ __('messages.people.empty') }}</flux:text>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('messages.people.columns.name') }}</flux:table.column>
                <flux:table.column>{{ __('messages.people.columns.codice_fiscale') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($people as $person)
                    <flux:table.row wire:key="person-{{ $person->id }}">
                        <flux:table.cell>{{ $person->first_name }} {{ $person->last_name }}</flux:table.cell>
                        <flux:table.cell class="font-mono">{{ $person->codice_fiscale?->value() }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <flux:pagination :paginator="$people" class="mt-4" />
    @endif
</div>
