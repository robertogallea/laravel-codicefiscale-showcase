@if ($updatedAt)
    <flux:text size="sm" variant="subtle">
        {{ __('messages.footer.data_freshness', ['date' => $updatedAt->format('d M Y')]) }}
    </flux:text>
@endif
