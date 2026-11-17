<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Nothing to register: the dev packages (Debugbar, IDE helper) register themselves through Composer auto-discovery
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Logging goes through the 'daily' channel set in .env (LOG_CHANNEL); nothing else to boot
    }
}
