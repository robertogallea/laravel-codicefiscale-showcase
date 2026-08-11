<?php

use Illuminate\Support\Facades\Blade;

it('slices a code into its five positional segments with a copy action', function () {
    $html = Blade::render('<x-codice-fiscale-breakdown :code="$code" />', ['code' => 'RSSMRA95E05F205Z']);

    expect($html)
        ->toContain('RSS')
        ->toContain('MRA')
        ->toContain('95E05')
        ->toContain('F205')
        ->toContain('>Z<')
        ->toContain(__('messages.breakdown.surname'))
        ->toContain(__('messages.breakdown.checksum'))
        ->toContain('Copy');
});
