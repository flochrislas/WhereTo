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
         // \Log::getMonolog()->popHandler();
         // \Log::useDailyFiles(storage_path('/logs/').php_sapi_name().'-'.get_current_user().'.log');
        // TODO: The above cannot be done above Laravel 5.5, check how to do, maybe just use daily log driver in config logging

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
