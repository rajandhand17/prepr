<?php

namespace App\Repositories\Api\Career;

use Illuminate\Support\ServiceProvider;

class CareerServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Career\CareerInterface', 'App\Repositories\Api\Career\CareerRepository');
    }
}
