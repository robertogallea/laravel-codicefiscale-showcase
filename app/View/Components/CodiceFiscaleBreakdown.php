<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Splits a 16-character codice fiscale into its five positional
 * segments for display - surname code, name code, date+gender code,
 * birthplace code, checksum - per the structure documented on
 * Robertogallea\CodiceFiscale\CodiceFiscale. Pure string slicing, no
 * dependency on the domain Parser: this only needs to know where the
 * segments sit, not what they decode to, so it works identically for
 * a freshly generated code or a code pasted in for decoding.
 */
class CodiceFiscaleBreakdown extends Component
{
    /**
     * [start, length] of each segment, in codice fiscale character
     * order - see this class's own docblock for where these come from.
     */
    private const SEGMENT_OFFSETS = [
        'surname' => [0, 3],
        'name' => [3, 3],
        'date_gender' => [6, 5],
        'birthplace' => [11, 4],
        'checksum' => [15, 1],
    ];

    /** @var list<array{key: string, value: string}> */
    public array $segments;

    public function __construct(public string $code)
    {
        $this->segments = array_map(
            fn (string $key, array $offset) => ['key' => $key, 'value' => substr($code, $offset[0], $offset[1])],
            array_keys(self::SEGMENT_OFFSETS),
            self::SEGMENT_OFFSETS,
        );
    }

    public function render(): View|Closure|string
    {
        return view('components.codice-fiscale-breakdown');
    }
}
