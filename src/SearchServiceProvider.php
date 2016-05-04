<?php

namespace Ltbl\ESSearchService;

use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/essearch.php' => config_path('essearch.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        
    }
}
