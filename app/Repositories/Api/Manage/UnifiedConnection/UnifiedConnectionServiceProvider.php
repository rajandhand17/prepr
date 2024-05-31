<?php

namespace App\Repositories\Api\Manage\UnifiedConnection;

use Illuminate\Support\ServiceProvider;

class UnifiedConnectionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Repositories\Api\Manage\UnifiedConnection\UnifiedConnectionInterface', 'App\Repositories\Api\Manage\UnifiedConnection\UnifiedConnectionRepository');
    }
}
