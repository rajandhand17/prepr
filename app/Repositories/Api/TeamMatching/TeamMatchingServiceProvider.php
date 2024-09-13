<?php

namespace App\Repositories\Api\TeamMatching;

use Illuminate\Support\ServiceProvider;

class TeamMatchingServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\TeamMatching\TeamMatchingInterface', 'App\Repositories\Api\TeamMatching\TeamMatchingRepository');
    }
}
