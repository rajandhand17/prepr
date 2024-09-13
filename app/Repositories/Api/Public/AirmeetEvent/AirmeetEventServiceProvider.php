<?php

namespace App\Repositories\Api\Public\AirmeetEvent;

use Illuminate\Support\ServiceProvider;

class AirmeetEventServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Public\AirmeetEvent\AirmeetEventInterface', 'App\Repositories\Api\Public\AirmeetEvent\AirmeetEventRepository');
    }
}
