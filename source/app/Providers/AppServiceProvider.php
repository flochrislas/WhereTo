<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Nothing originally
        // Add setting to separate log files
        \Log::getMonolog()->popHandler();
        \Log::useDailyFiles(storage_path('/logs/').php_sapi_name().'-'.get_current_user().'.log');
        // Then add this to help PhpStorm and IDE
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
