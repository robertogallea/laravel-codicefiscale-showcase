<?php

return [

    'home' => [
        'tagline' => 'Una vetrina di tutte le funzionalità di laravel-codicefiscale v3.',
    ],

    'footer' => [
        'data_freshness' => 'Dati sui luoghi di nascita aggiornati al :date',
    ],

    'theme' => [
        'label' => 'Tema',
        'light' => 'Chiaro',
        'dark' => 'Scuro',
        'system' => 'Sistema',
    ],

    'hero' => [
        'fields' => [
            'first_name' => 'Nome',
            'last_name' => 'Cognome',
            'birth_date' => 'Data di nascita',
            'gender' => 'Sesso',
            'gender_male' => 'Maschio',
            'gender_female' => 'Femmina',
            'birth_place' => 'Luogo di nascita',
            'birth_place_placeholder' => 'Cerca un comune o uno stato estero…',
            'birth_place_unresolved' => 'Seleziona un luogo di nascita dai suggerimenti.',
        ],
        'calculate' => [
            'tab' => 'Calcola',
            'submit' => 'Genera',
            'verify' => 'Verifica questo codice',
        ],
        'decode' => [
            'tab' => 'Decodifica',
            'input' => 'Codice fiscale',
            'input_placeholder' => 'es. RSSMRA95E05F205Z',
            'submit' => 'Decodifica',
        ],
    ],

    'decode' => [
        'errors' => [
            'invalid_format' => 'Non è un codice fiscale ben formato (lunghezza o caratteri errati).',
            'invalid_checksum' => 'Il carattere di controllo non corrisponde al resto del codice — controlla che non ci sia un errore di battitura.',
            'invalid_date' => 'La data di nascita codificata non esiste nel calendario.',
            'unknown_birth_place' => 'Il codice del luogo di nascita codificato non è riconosciuto.',
            'birth_place_not_valid_on_date' => 'Il luogo di nascita codificato non era valido alla data di nascita codificata.',
        ],
        'result' => [
            'gender' => 'Sesso',
            'gender_male' => 'Maschio',
            'gender_female' => 'Femmina',
            'birth_date' => 'Data di nascita',
            'birth_place' => 'Luogo di nascita',
            'birth_place_unknown' => 'Sconosciuto',
            'omocodia' => 'Variante omocodica',
            'yes' => 'Sì',
            'no' => 'No',
        ],
        'match' => [
            'toggle' => 'Verifica anche con una persona',
            'submit' => 'Verifica',
            'gender_unspecified' => 'Non specificato',
            'matched' => 'Corrispondente',
            'mismatched' => 'Non corrispondente',
            'skipped' => 'Non verificato',
        ],
    ],

    'breakdown' => [
        'surname' => 'Codice cognome — le prime tre consonanti del cognome.',
        'name' => 'Codice nome — tre lettere prese dal nome.',
        'date_gender' => 'Anno, mese e giorno di nascita — al giorno si aggiunge 40 per il sesso femminile.',
        'birthplace' => 'Codice catastale del comune o dello stato estero di nascita.',
        'checksum' => 'Carattere di controllo — calcolato sugli altri 15, serve a rilevare errori di battitura.',
        'copy' => 'Copia',
        'copied' => 'Copiato!',
    ],

];
