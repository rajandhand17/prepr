<?php

namespace App\Repositories\Api\Public\ResourceCollection;

use Illuminate\Support\ServiceProvider;

class ResourceCollectionServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Public\ResourceCollection\ResourceCollectionInterface', 'App\Repositories\Api\Public\ResourceCollection\ResourceCollectionRepository');
    }
}
