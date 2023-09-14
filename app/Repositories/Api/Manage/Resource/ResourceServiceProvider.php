<?php

namespace App\Repositories\Api\Manage\Resource;

use Illuminate\Support\ServiceProvider;

class ResourceServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Resource\ResourceInterface', 'App\Repositories\Api\Manage\Resource\ResourceRepository');
    }
}
