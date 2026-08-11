<?php

namespace App\Http\Controllers\Explore;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BirthDateResolverController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        return view('explore.birth-date-resolver');
    }
}
