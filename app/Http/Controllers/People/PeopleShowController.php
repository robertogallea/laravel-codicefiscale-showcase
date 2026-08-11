<?php

namespace App\Http\Controllers\People;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Contracts\View\View;
use Robertogallea\CodiceFiscale\Contracts\BirthPlaceRepository;
use Robertogallea\CodiceFiscale\Parsing\Parser;

class PeopleShowController extends Controller
{
    /**
     * Re-parses the stored codice_fiscale independently of
     * CodiceFiscaleCast (which only turns the column into a
     * CodiceFiscale instance) - Parser::parse() decodes that same
     * instance further, so the cast and the domain parser are shown
     * agreeing on the exact same stored string.
     */
    public function __invoke(Person $person): View
    {
        $parsed = (new Parser(app(BirthPlaceRepository::class)))->parse($person->codice_fiscale);

        return view('people.show', [
            'person' => $person,
            'parsed' => $parsed,
        ]);
    }
}
