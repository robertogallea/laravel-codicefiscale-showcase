<?php

return [

    'home' => [
        'tagline' => 'A showcase of every laravel-codicefiscale v3 feature.',
    ],

    'footer' => [
        'data_freshness' => 'Birthplace data as of :date',
    ],

    'theme' => [
        'label' => 'Theme',
        'light' => 'Light',
        'dark' => 'Dark',
        'system' => 'System',
    ],

    'hero' => [
        // Shared by every hero-tool form that collects a person's own
        // data (Calculate, and the Decode person-matching disclosure) -
        // kept out of 'calculate' so it's clearly shared copy, not one
        // feature's private strings that another feature quietly reuses.
        'fields' => [
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'birth_date' => 'Birth date',
            'gender' => 'Gender',
            'gender_male' => 'Male',
            'gender_female' => 'Female',
            'birth_place' => 'Birth place',
            'birth_place_placeholder' => 'Search a comune or country…',
            'birth_place_unresolved' => 'Select a birthplace from the suggestions.',
        ],
        'calculate' => [
            'tab' => 'Calculate',
            'submit' => 'Generate',
            'verify' => 'Verify this code',
        ],
        'decode' => [
            'tab' => 'Decode',
            'input' => 'Codice fiscale',
            'input_placeholder' => 'e.g. RSSMRA95E05F205Z',
            'submit' => 'Decode',
        ],
    ],

    'decode' => [
        'errors' => [
            'invalid_format' => 'This is not a well-formed codice fiscale (wrong length or characters).',
            'invalid_checksum' => 'The check character doesn\'t match the rest of the code — check for a typo.',
            'invalid_date' => 'The encoded birth date doesn\'t exist on the calendar.',
            'unknown_birth_place' => 'The encoded birthplace code isn\'t recognized.',
            'birth_place_not_valid_on_date' => 'The encoded birthplace wasn\'t valid on the encoded birth date.',
        ],
        'result' => [
            'gender' => 'Gender',
            'gender_male' => 'Male',
            'gender_female' => 'Female',
            'birth_date' => 'Birth date',
            'birth_place' => 'Birth place',
            'birth_place_unknown' => 'Unknown',
            'omocodia' => 'Omocodia variant',
            'yes' => 'Yes',
            'no' => 'No',
        ],
        'match' => [
            'toggle' => 'Also check against a person',
            'submit' => 'Check',
            'gender_unspecified' => 'Unspecified',
            'matched' => 'Matched',
            'mismatched' => 'Mismatched',
            'skipped' => 'Skipped',
        ],
    ],

    'omocodia' => [
        'heading' => 'Omocodia playground',
        'tagline' => 'When two people would generate the same code, the Agenzia delle Entrate resolves the collision by substituting some of 7 fixed digit positions with letters. Toggle any of them to see a real, equivalent variant.',
        'code_label' => 'Codice fiscale',
        'load' => 'Load',
        'invalid_format' => 'This is not a well-formed codice fiscale (wrong length or characters).',
        'canonical_form' => 'Canonical form',
        'level' => 'Substitution level',
        'variant' => 'Variant code',
        'equivalent' => 'Equivalent to canonical form',
        'yes' => 'Yes',
        'no' => 'No',
        'positions' => [
            'year_1' => 'Year (1st digit)',
            'year_2' => 'Year (2nd digit)',
            'day_1' => 'Day (1st digit)',
            'day_2' => 'Day (2nd digit)',
            'birthplace_1' => 'Birthplace code (1st digit)',
            'birthplace_2' => 'Birthplace code (2nd digit)',
            'birthplace_3' => 'Birthplace code (3rd digit)',
        ],
    ],

    'breakdown' => [
        'surname' => 'Surname code — the first three consonants of the surname.',
        'name' => 'Name code — three letters taken from the first name.',
        'date_gender' => 'Birth year, month, and day — the day is offset by 40 for a female birth date.',
        'birthplace' => 'Birthplace code — the cadastral code of the comune or country of birth.',
        'checksum' => 'Check character — computed from the other 15, used to detect typos.',
        'copy' => 'Copy',
        'copied' => 'Copied!',
    ],

];
