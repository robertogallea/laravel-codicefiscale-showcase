<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Robertogallea\CodiceFiscale\Laravel\Commands\UpdatePlacesCommand;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // laravel-codicefiscale only registers this command when
        // $this->app->runningInConsole() is true, so Artisan::call()
        // can't find it from RefreshCodiceFiscaleDataController, which
        // runs inside a normal web request. Register it ourselves,
        // unconditionally, so the scheduler endpoint can invoke it.
        $this->commands([
            UpdatePlacesCommand::class,
        ]);
    }
}
