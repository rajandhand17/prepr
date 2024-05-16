<?php

namespace App\Repositories\Api\Manage\AirmeetEvent;

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
        $this->app->bind('App\Repositories\Api\Manage\AirmeetEvent\AirmeetEventInterface', 'App\Repositories\Api\Manage\AirmeetEvent\AirmeetEventRepository');
    }
}
