<?php

use App\Http\Controllers\Explore\OmocodiaPlaygroundController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/locale/{locale}', LocaleController::class)
    ->whereIn('locale', config('app.supported_locales'))
    ->name('locale.switch');

Route::get('/explore/omocodia', OmocodiaPlaygroundController::class)->name('explore.omocodia');
