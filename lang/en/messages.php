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

    'repository_swap' => [
        'heading' => 'Swap birthplace repositories',
        'tagline' => 'The same fixed code below is resolved through two different implementations of the same BirthPlaceRepository contract — proof that generation, validation, and parsing never depend on any one data source.',
        'repository_label' => 'Birthplace repository',
        'real_label' => 'Real repository (ANPR/MAECI data)',
        'demo_label' => 'Demo repository (fictional places)',
        'demo_notice' => 'The Demo repository is a hand-authored fixture — Atlantis, Narnia, Wakanda — that exists only on this page, to prove the repository contract is swappable. It is never used anywhere else in this app: Calculate and Decode always use the Real repository.',
        'code_label' => 'Demonstration code',
    ],

    'birth_date_resolver' => [
        'heading' => 'Birth-year resolution playground',
        'tagline' => 'A codice fiscale\'s two-digit birth year is inherently ambiguous — "26" could mean 1926 or 2026. Adjust the inputs below to see how the default resolver picks between the two candidates, and when it can\'t.',
        'month_label' => 'Month',
        'day_label' => 'Day',
        'two_digit_year_label' => 'Two-digit year',
        'max_age_label' => 'Maximum plausible age',
        'possible_years_label' => 'Possible years',
        'resolved_date_label' => 'Resolved birth date',
        'no_plausible_date' => 'No plausible date — neither candidate satisfies the maximum age, or both fall after today.',
        'tiebreak_note' => 'When both candidates are otherwise equally plausible, the resolver also checks birthplace history: a birthplace valid at exactly one of the two candidate dates settles the tie. Try switching the birthplace to see this in action.',
    ],

    'birthplace_history' => [
        'heading' => 'Birthplace history',
        'tagline' => 'A comune that renamed itself or changed province keeps its cadastral code across the change — the birthplace domain models this as multiple time-bounded records sharing one code, resolved by date. The worked example, Piperno, had both: renamed Priverno on 9 September 1928, then reprovinced from Roma to Latina on 26 October 1934.',
        'date_label' => 'Birth date',
        'comune_section' => 'Historical resolution — G698 (Piperno → Priverno)',
        'never_existed_section' => 'Never existed vs. existed, wrong date — X999',
        'name_label' => 'Name',
        'province_label' => 'Province',
        'not_valid_on_date' => 'Existed, but not valid on this date.',
        'never_existed' => 'Never existed.',
    ],

    'validation_rule' => [
        'heading' => 'The plain validation rule',
        'tagline' => 'Every other page on this site calls the domain Validator directly and gets back a typed list of ValidationError cases to build its own UI around. This page is the opposite: a real Laravel form, validated server-side with nothing but the bare codice_fiscale rule string plugged into $request->validate() — no domain object ever touches this page\'s code. The rule is built on the very same Validator, so the message text is just as specific, but the app never sees a ValidationError to act on: it only gets Laravel\'s ordinary $errors bag. See Decode for the richer, programmatic version.',
        'field_label' => 'Codice fiscale',
        'field_placeholder' => 'e.g. RSSMRA95E05F205Z',
        'submit' => 'Validate',
        'valid_message' => 'Valid — :code passed validation.',
    ],

    'people' => [
        'heading' => 'People directory',
        'tagline' => 'A real Eloquent model, App\Models\Person, storing each row\'s codice_fiscale through CodiceFiscaleCast. The seeded rows are a deliberate mix: most were produced with the Faker provider for bulk random data, and a few were built by generating a code directly from a specific Person value object, so re-parsing those rows\' own codice_fiscale reproduces the exact data they were generated from.',
        'columns' => [
            'name' => 'Name',
            'codice_fiscale' => 'Codice fiscale',
        ],
        'empty' => 'No people yet — run the database seeder.',
        'new' => 'New person',
        'back_to_list' => 'Back to the list',
        'fields' => [
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'birth_date' => 'Birth date',
            'gender' => 'Gender',
            'gender_male' => 'Male',
            'gender_female' => 'Female',
            'birth_place' => 'Birth place',
            'birth_place_placeholder' => 'Search a comune or country…',
            'codice_fiscale' => 'Codice fiscale',
            'codice_fiscale_placeholder' => 'e.g. RSSMRA95E05F205Z',
            'codice_fiscale_taken' => 'A person with this codice fiscale already exists.',
        ],
        'create' => [
            'heading' => 'Add a person',
            'tagline' => 'Validated by CodiceFiscaleRule::make()->matching() - the fluent, cross-field-aware form of the codice_fiscale rule. Unlike the plain rule on the validation-rule Explore page, this checks the submitted code against every personal field below, and fails on the specific field that doesn\'t match rather than a generic error.',
            'submit' => 'Create',
        ],
        'show' => [
            'reparse_heading' => 'Live re-parse',
            'reparse_tagline' => 'The breakdown below and the fields under it are decoded straight from this person\'s own stored codice_fiscale, independently of the first name/last name columns - the same Parser used by the hero tool\'s Decode mode, agreeing with what CodiceFiscaleCast already turned the column into.',
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
