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
        'calculate' => [
            'tab' => 'Calculate',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'birth_date' => 'Birth date',
            'gender' => 'Gender',
            'gender_male' => 'Male',
            'gender_female' => 'Female',
            'birth_place' => 'Birth place',
            'birth_place_placeholder' => 'Search a comune or country…',
            'birth_place_unresolved' => 'Select a birthplace from the suggestions.',
            'submit' => 'Generate',
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
