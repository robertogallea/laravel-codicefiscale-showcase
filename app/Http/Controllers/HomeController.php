<?php

namespace App\Http\Controllers;

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
     * One card per feature area listed in the parent spec (#1) - Calculate,
     * Decode, and person-matching all live inside the same HeroTool
     * component, deep-linked via its mode/match URL-bound properties
     * rather than a separate route.
     *
     * @return list<array{route: string, heading: string, description: string}>
     */
    private function features(): array
    {
        return [
            [
                'route' => route('home'),
                'heading' => __('messages.home.features.calculate.heading'),
                'description' => __('messages.home.features.calculate.description'),
            ],
            [
                'route' => route('home', ['mode' => 'decode']),
                'heading' => __('messages.home.features.decode.heading'),
                'description' => __('messages.home.features.decode.description'),
            ],
            [
                'route' => route('home', ['mode' => 'decode', 'match' => 1]),
                'heading' => __('messages.home.features.person_matching.heading'),
                'description' => __('messages.home.features.person_matching.description'),
            ],
            [
                'route' => route('explore.omocodia'),
                'heading' => __('messages.home.features.omocodia.heading'),
                'description' => __('messages.home.features.omocodia.description'),
            ],
            [
                'route' => route('explore.repositories'),
                'heading' => __('messages.home.features.repository_swap.heading'),
                'description' => __('messages.home.features.repository_swap.description'),
            ],
            [
                'route' => route('explore.birth-date-resolver'),
                'heading' => __('messages.home.features.birth_date_resolver.heading'),
                'description' => __('messages.home.features.birth_date_resolver.description'),
            ],
            [
                'route' => route('explore.birthplace-history'),
                'heading' => __('messages.home.features.birthplace_history.heading'),
                'description' => __('messages.home.features.birthplace_history.description'),
            ],
            [
                'route' => route('explore.validation-rule'),
                'heading' => __('messages.home.features.validation_rule.heading'),
                'description' => __('messages.home.features.validation_rule.description'),
            ],
            [
                'route' => route('people.index'),
                'heading' => __('messages.home.features.people.heading'),
                'description' => __('messages.home.features.people.description'),
            ],
        ];
    }
}
