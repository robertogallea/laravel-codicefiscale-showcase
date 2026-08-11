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

    'omocodia' => [
        'heading' => 'Omocodia',
        'tagline' => 'Quando due persone genererebbero lo stesso codice, l\'Agenzia delle Entrate risolve la collisione sostituendo alcune di 7 posizioni numeriche fisse con lettere. Attivane una qualsiasi per vedere una variante reale ed equivalente.',
        'code_label' => 'Codice fiscale',
        'load' => 'Carica',
        'invalid_format' => 'Non è un codice fiscale ben formato (lunghezza o caratteri errati).',
        'canonical_form' => 'Forma canonica',
        'level' => 'Livello di sostituzione',
        'variant' => 'Codice variante',
        'equivalent' => 'Equivalente alla forma canonica',
        'yes' => 'Sì',
        'no' => 'No',
        'positions' => [
            'year_1' => 'Anno (1° cifra)',
            'year_2' => 'Anno (2° cifra)',
            'day_1' => 'Giorno (1° cifra)',
            'day_2' => 'Giorno (2° cifra)',
            'birthplace_1' => 'Codice catastale (1° cifra)',
            'birthplace_2' => 'Codice catastale (2° cifra)',
            'birthplace_3' => 'Codice catastale (3° cifra)',
        ],
    ],

    'repository_swap' => [
        'heading' => 'Scambia i repository dei luoghi di nascita',
        'tagline' => 'Lo stesso codice fisso qui sotto viene risolto attraverso due diverse implementazioni dello stesso contratto BirthPlaceRepository — la prova che generazione, validazione e parsing non dipendono mai da un\'unica fonte di dati.',
        'repository_label' => 'Repository dei luoghi di nascita',
        'real_label' => 'Repository reale (dati ANPR/MAECI)',
        'demo_label' => 'Repository dimostrativo (luoghi immaginari)',
        'demo_notice' => 'Il repository dimostrativo è una struttura fittizia scritta a mano — Atlantide, Narnia, Wakanda — che esiste solo in questa pagina, per dimostrare che il contratto del repository è scambiabile. Non viene mai usato altrove in questa app: Calcola e Decodifica usano sempre il repository reale.',
        'code_label' => 'Codice dimostrativo',
    ],

    'birth_date_resolver' => [
        'heading' => 'Risoluzione dell\'anno di nascita',
        'tagline' => 'L\'anno di nascita a due cifre di un codice fiscale è per natura ambiguo — "26" potrebbe significare 1926 o 2026. Modifica i valori qui sotto per vedere come il resolver predefinito sceglie tra i due candidati, e quando non riesce a farlo.',
        'month_label' => 'Mese',
        'day_label' => 'Giorno',
        'two_digit_year_label' => 'Anno a due cifre',
        'max_age_label' => 'Età massima plausibile',
        'possible_years_label' => 'Anni possibili',
        'resolved_date_label' => 'Data di nascita risolta',
        'no_plausible_date' => 'Nessuna data plausibile — nessuno dei due candidati soddisfa l\'età massima, oppure entrambi cadono dopo oggi.',
        'tiebreak_note' => 'Quando entrambi i candidati sono altrimenti ugualmente plausibili, il resolver controlla anche la storia del luogo di nascita: un luogo valido esattamente a una delle due date candidate risolve la parità. Prova a cambiare il luogo di nascita per vederlo in azione.',
    ],

    'birthplace_history' => [
        'heading' => 'Storia dei luoghi di nascita',
        'tagline' => 'Un comune che ha cambiato nome o provincia mantiene il proprio codice catastale attraverso il cambiamento — il dominio dei luoghi di nascita lo modella come più record delimitati nel tempo che condividono un codice, risolti in base alla data. L\'esempio scelto, Piperno, ha avuto entrambi: rinominato Priverno il 9 settembre 1928, poi passato dalla provincia di Roma a quella di Latina il 26 ottobre 1934.',
        'date_label' => 'Data di nascita',
        'comune_section' => 'Risoluzione storica — G698 (Piperno → Priverno)',
        'never_existed_section' => 'Mai esistito o esistito ma non a questa data — X999',
        'name_label' => 'Nome',
        'province_label' => 'Provincia',
        'not_valid_on_date' => 'Esisteva, ma non era valido a questa data.',
        'never_existed' => 'Mai esistito.',
    ],

    'validation_rule' => [
        'heading' => 'La regola di validazione elementare',
        'tagline' => 'Ogni altra pagina di questo sito chiama direttamente il Validator del dominio e ottiene una lista tipizzata di casi ValidationError su cui costruire la propria interfaccia. Questa pagina è l\'opposto: un vero form Laravel, validato lato server con nient\'altro che la regola codice_fiscale passata a $request->validate() — nessun oggetto del dominio tocca mai il codice di questa pagina. La regola è costruita sullo stesso identico Validator, quindi il testo del messaggio è altrettanto specifico, ma l\'app non vede mai un ValidationError su cui agire: riceve solo l\'ordinario error bag di Laravel. Vedi Decodifica per la versione più ricca e programmabile.',
        'field_label' => 'Codice fiscale',
        'field_placeholder' => 'es. RSSMRA95E05F205Z',
        'submit' => 'Valida',
        'valid_message' => 'Valido — :code ha superato la validazione.',
    ],

    'people' => [
        'heading' => 'Rubrica delle persone',
        'tagline' => 'Un vero modello Eloquent, App\Models\Person, che memorizza il codice fiscale di ogni riga tramite CodiceFiscaleCast. Le righe seminate sono un mix deliberato: la maggior parte è stata prodotta con il provider Faker per dati casuali in blocco, e alcune sono state costruite generando un codice direttamente da un preciso value object Person, così ridecodificare il codice fiscale di quelle righe ne riproduce esattamente i dati di partenza.',
        'columns' => [
            'name' => 'Nome',
            'codice_fiscale' => 'Codice fiscale',
        ],
        'empty' => 'Ancora nessuna persona — esegui il seeder del database.',
        'new' => 'Nuova persona',
        'back_to_list' => 'Torna all\'elenco',
        'fields' => [
            'first_name' => 'Nome',
            'last_name' => 'Cognome',
            'birth_date' => 'Data di nascita',
            'gender' => 'Sesso',
            'gender_male' => 'Maschio',
            'gender_female' => 'Femmina',
            'birth_place' => 'Luogo di nascita',
            'birth_place_placeholder' => 'Cerca un comune o uno stato estero…',
            'codice_fiscale' => 'Codice fiscale',
            'codice_fiscale_placeholder' => 'es. RSSMRA95E05F205Z',
            'codice_fiscale_taken' => 'Esiste già una persona con questo codice fiscale.',
        ],
        'create' => [
            'heading' => 'Aggiungi una persona',
            'tagline' => 'Validato da CodiceFiscaleRule::make()->matching() - la forma fluente e cross-field della regola codice_fiscale. A differenza della regola elementare nella pagina Explore sulla validazione, questa controlla il codice inserito rispetto a ciascun campo personale qui sotto, e fallisce indicando lo specifico campo che non corrisponde, non un errore generico.',
            'submit' => 'Crea',
        ],
        'show' => [
            'reparse_heading' => 'Ridecodifica dal vivo',
            'reparse_tagline' => 'Lo schema qui sotto e i campi che seguono sono decodificati direttamente dal codice fiscale memorizzato per questa persona, indipendentemente dalle colonne nome/cognome - lo stesso Parser usato dalla modalità Decodifica dello strumento principale, che concorda con ciò che CodiceFiscaleCast ha già ricavato dalla colonna.',
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
