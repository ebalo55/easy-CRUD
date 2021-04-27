<?php

namespace Ebalo\EasyCRUD;

use Ebalo\EasyCRUD\Commands\Install;
use Illuminate\Support\ServiceProvider;

class EasyCRUDServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // Registering package commands.
            $this->commands([
                Install::class
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {  }
}
