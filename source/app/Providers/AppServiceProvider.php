<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        // The 'admin' gate: who may use the admin section. Checked by the 'can:admin' middleware on the admin
        // routes (403 when it says no). users.level is a string column; the seeded admins have '1'.
        Gate::define('admin', fn (User $user): bool => (int) $user->level >= 1);
        // Logging goes through the 'daily' channel set in .env (LOG_CHANNEL); nothing else to boot
    }
}
