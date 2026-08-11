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
        'calculate' => [
            'tab' => 'Calcola',
            'first_name' => 'Nome',
            'last_name' => 'Cognome',
            'birth_date' => 'Data di nascita',
            'gender' => 'Sesso',
            'gender_male' => 'Maschio',
            'gender_female' => 'Femmina',
            'birth_place' => 'Luogo di nascita',
            'birth_place_placeholder' => 'Cerca un comune o uno stato estero…',
            'birth_place_unresolved' => 'Seleziona un luogo di nascita dai suggerimenti.',
            'submit' => 'Genera',
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
