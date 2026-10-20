<?php

return [
    'update_token' => env('CODICEFISCALE_UPDATE_TOKEN'),

    'database' => [
        // Path to the dedicated SQLite file backing the birthplace
        // repository. Never the host application's own database -
        // installing/using this package doesn't touch it. Set this
        // to ':memory:' (e.g. in tests) for an ephemeral database.
        'path' => storage_path('app/codicefiscale/places.sqlite'),
    ],

    // Sources for `codice-fiscale:update-places`. Never fetched
    // during normal validation - only when this command is run.
    // ANPR's WAF rejects requests from Render's network, so production
    // overrides these with copies the weekly GitHub workflow mirrors
    // to the `anpr-sources` release (see render.yaml).
    'sources' => [
        'municipalities' => env('CODICEFISCALE_MUNICIPALITIES_SOURCE', 'https://www.anagrafenazionale.interno.it/wp-content/uploads/ANPR_archivio_comuni.csv'),
        'countries' => env('CODICEFISCALE_COUNTRIES_SOURCE', 'https://www.anagrafenazionale.interno.it/wp-content/uploads/tabella_2_statiesteri.xlsx'),
    ],
];
