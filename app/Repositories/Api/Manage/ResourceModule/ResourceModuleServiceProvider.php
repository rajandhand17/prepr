<?php

namespace App\Repositories\Api\Manage\ResourceModule;

use Illuminate\Support\ServiceProvider;

class ResourceModuleServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\ResourceModule\ResourceModuleInterface', 'App\Repositories\Api\Manage\ResourceModule\ResourceCollectionRepository');
    }
}
