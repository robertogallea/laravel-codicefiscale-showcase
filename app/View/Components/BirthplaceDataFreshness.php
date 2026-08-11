<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BirthplaceDataFreshness extends Component
{
    public ?\DateTimeImmutable $updatedAt;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->updatedAt = $this->resolveUpdatedAt();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.birthplace-data-freshness');
    }

    /**
     * The freshness marker is the birthplace database file's own mtime,
     * not a separately recorded value - `codice-fiscale:update-places`
     * only ever writes to this file when it completes, so the file's
     * last-modified time already is the completion marker.
     */
    private function resolveUpdatedAt(): ?\DateTimeImmutable
    {
        $path = config('codicefiscale.database.path');

        if (! is_string($path) || $path === ':memory:' || ! file_exists($path)) {
            return null;
        }

        $timestamp = filemtime($path);

        return $timestamp === false ? null : (new \DateTimeImmutable)->setTimestamp($timestamp);
    }
}
