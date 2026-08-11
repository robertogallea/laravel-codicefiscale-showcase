<?php

namespace App\Livewire\Explore;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Robertogallea\CodiceFiscale\CodiceFiscale;
use Robertogallea\CodiceFiscale\Omocodia\Omocodia;

class OmocodiaPlayground extends Component
{
    /**
     * The 7 omocodia-eligible positions (0-indexed within the 16-char
     * code) and the letters a digit may be substituted with there -
     * public, stable facts about the codice fiscale format itself
     * (documented on Omocodia's own docblock: "year, day, birthplace-
     * code digits" substituted with one of "L,M,N,P,Q,R,S,T,U,V"),
     * not a reach into the package's private implementation. Needed
     * here because Omocodia has no public way to build one specific
     * chosen variant directly - only canonical(), level(), and
     * variants() (all 128, unordered by any documented guarantee).
     *
     * These assumptions are cross-checked against the real package in
     * tests/Feature/OmocodiaPlaygroundTest.php ("the eligible position
     * and substitution-letter assumptions hold for every real
     * variant"), so drift from a future package version fails loudly
     * in CI rather than silently mislabeling a toggle.
     */
    private const ELIGIBLE_POSITIONS = [6, 7, 9, 10, 12, 13, 14];

    private const SUBSTITUTION_LETTERS = ['L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V'];

    /**
     * Translation-independent keys, in the same order as
     * ELIGIBLE_POSITIONS, so the view can zip labels to toggles
     * without depending on lang-file array ordering.
     */
    private const POSITION_KEYS = ['year_1', 'year_2', 'day_1', 'day_2', 'birthplace_1', 'birthplace_2', 'birthplace_3'];

    public string $code = 'RSSMRA95E05F205Z';

    public ?string $error = null;

    public ?string $canonicalCode = null;

    public ?int $level = null;

    /** @var list<bool> one flag per ELIGIBLE_POSITIONS/POSITION_KEYS entry, in order */
    public array $toggles = [false, false, false, false, false, false, false];

    public ?string $variantCode = null;

    public ?bool $isEquivalent = null;

    public function mount(): void
    {
        $this->loadCode();
    }

    public function loadCode(): void
    {
        $this->error = null;
        $this->canonicalCode = null;
        $this->level = null;
        $this->variantCode = null;
        $this->isEquivalent = null;

        $cf = CodiceFiscale::tryFrom($this->code);

        if ($cf === null) {
            $this->error = __('messages.omocodia.invalid_format');

            return;
        }

        $omocodia = new Omocodia;
        $this->canonicalCode = $omocodia->canonical($cf)->value();
        $this->level = $omocodia->level($cf);
        $this->toggles = $this->substitutionSignature($cf->value());

        $this->recomputeVariant();
    }

    public function toggle(int $index): void
    {
        $this->toggles[$index] = ! $this->toggles[$index];
        $this->recomputeVariant();
    }

    /** @return list<array{key: string, label: string}> */
    public function positions(): array
    {
        return array_map(
            fn (string $key) => ['key' => $key, 'label' => __('messages.omocodia.positions.'.$key)],
            self::POSITION_KEYS,
        );
    }

    public function render(): View
    {
        return view('livewire.explore.omocodia-playground');
    }

    private function recomputeVariant(): void
    {
        if ($this->canonicalCode === null) {
            return;
        }

        $canonical = CodiceFiscale::from($this->canonicalCode);

        foreach ((new Omocodia)->variants($canonical) as $variant) {
            if ($this->substitutionSignature($variant->value()) === $this->toggles) {
                $this->variantCode = $variant->value();
                $this->isEquivalent = $variant->isEquivalentTo($canonical);

                return;
            }
        }

        // Unreachable if ELIGIBLE_POSITIONS/SUBSTITUTION_LETTERS still
        // match the package's real behavior: variants() yields exactly
        // one member per possible signature. Failing loudly here (verified
        // in tests) is deliberately preferred over silently leaving
        // variantCode/isEquivalent stale if that assumption ever drifts.
        throw new \LogicException('No Omocodia variant matched the requested substitution signature.');
    }

    /** @return list<bool> */
    private function substitutionSignature(string $codeValue): array
    {
        return array_map(
            fn (int $position) => in_array($codeValue[$position], self::SUBSTITUTION_LETTERS, true),
            self::ELIGIBLE_POSITIONS,
        );
    }
}
