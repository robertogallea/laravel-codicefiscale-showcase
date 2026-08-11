<?php

namespace App\Http\Controllers\Explore;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Deliberately the only page in this app that validates a codice
 * fiscale through a real Laravel form request rather than calling the
 * domain Validator directly - see resources/views/explore/validation-rule.blade.php
 * for the contrast this demonstrates.
 */
class ValidationRuleController extends Controller
{
    /**
     * Show the form.
     */
    public function show(): View
    {
        return view('explore.validation-rule');
    }

    /**
     * Validate the submitted codice fiscale.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codice_fiscale' => 'codice_fiscale',
        ]);

        return redirect()
            ->route('explore.validation-rule')
            ->with('validatedCode', $validated['codice_fiscale']);
    }
}
