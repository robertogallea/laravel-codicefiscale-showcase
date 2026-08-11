<?php

use App\Http\Controllers\Explore\BirthDateResolverController;
use App\Http\Controllers\Explore\BirthplaceHistoryController;
use App\Http\Controllers\Explore\OmocodiaPlaygroundController;
use App\Http\Controllers\Explore\RepositorySwapController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/locale/{locale}', LocaleController::class)
    ->whereIn('locale', config('app.supported_locales'))
    ->name('locale.switch');

Route::get('/explore/omocodia', OmocodiaPlaygroundController::class)->name('explore.omocodia');
Route::get('/explore/repositories', RepositorySwapController::class)->name('explore.repositories');
Route::get('/explore/birth-date-resolver', BirthDateResolverController::class)->name('explore.birth-date-resolver');
Route::get('/explore/birthplace-history', BirthplaceHistoryController::class)->name('explore.birthplace-history');
