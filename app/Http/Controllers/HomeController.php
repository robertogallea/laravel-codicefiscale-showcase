<?php

namespace App\Http\Controllers;

use App\Support\FeatureCatalog;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        return view('home', ['features' => $this->features()]);
    }

    /**
     * One index row per feature area listed in the parent spec (#1) -
     * Calculate, Decode, and person-matching all live inside the same
     * HeroTool component, deep-linked via its mode/match URL-bound
     * properties rather than a separate route. Sourced from
     * FeatureCatalog so the homepage index and every feature page's
     * prev/next rail always agree on the same ordered list.
     *
     * @return list<array{route: string, heading: string, description: string, quadro: string}>
     */
    private function features(): array
    {
        return array_map(
            fn (array $entry): array => [
                'route' => route($entry['routeName'], $entry['routeParams']),
                'heading' => __($entry['headingKey']),
                'description' => __($entry['descriptionKey']),
                'quadro' => $entry['quadro'],
            ],
            FeatureCatalog::entries(),
        );
    }
}
