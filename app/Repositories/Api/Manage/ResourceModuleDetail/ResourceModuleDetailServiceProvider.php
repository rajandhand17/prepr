<?php

namespace App\Repositories\Api\Manage\ResourceModule;

use Illuminate\Support\ServiceProvider;

class ResourceModuleDetailServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\ResourceModuleDetail\ResourceModuleDetailInterface', 'App\Repositories\Api\Manage\ResourceModuleDetail\ResourceModuleDetailRepository');
    }
}
