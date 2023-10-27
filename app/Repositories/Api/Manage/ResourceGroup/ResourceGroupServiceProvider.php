<?php

namespace App\Repositories\Api\Manage\ResourceGroup;

use Illuminate\Support\ServiceProvider;

class ResourceGroupServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\ResourceGroup\ResourceGroupInterface', 'App\Repositories\Api\Manage\ResourceGroup\ResourceGroupRepository');
    }
}
