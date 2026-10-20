<?php

return [
    'database' => [
        // Path to the dedicated SQLite file backing the birthplace
        // repository. Never the host application's own database -
        // installing/using this package doesn't touch it. Set this
        // to ':memory:' (e.g. in tests) for an ephemeral database.
        'path' => storage_path('app/codicefiscale/places.sqlite'),
    ],

    // Sources for `codice-fiscale:update-places`. Never fetched
    // during normal validation - only when this command is run.
    // ANPR's WAF rejects requests from Render's network, so the Docker
    // build imports copies mirrored to the `anpr-sources` release with
    // `codice-fiscale:import-places` instead.
    'sources' => [
        'municipalities' => 'https://www.anagrafenazionale.interno.it/wp-content/uploads/ANPR_archivio_comuni.csv',
        'countries' => 'https://www.anagrafenazionale.interno.it/wp-content/uploads/tabella_2_statiesteri.xlsx',
    ],
];
