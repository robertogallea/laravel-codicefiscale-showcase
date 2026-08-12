<?php

use Illuminate\Support\Facades\Artisan;

it('rejects a codice fiscale data refresh without the scheduler token', function () {
    config()->set('codicefiscale.update_token', 'expected-token');

    $this->post(route('internal.codice-fiscale-update'))
        ->assertForbidden();
});

it('runs the update command for an authorized scheduler request', function () {
    config()->set('codicefiscale.update_token', 'expected-token');
    Artisan::shouldReceive('call')
        ->once()
        ->with('codice-fiscale:update-places')
        ->andReturn(0);

    $this->post(route('internal.codice-fiscale-update'), [], [
        'X-Codicefiscale-Update-Token' => 'expected-token',
    ])->assertNoContent();
});
