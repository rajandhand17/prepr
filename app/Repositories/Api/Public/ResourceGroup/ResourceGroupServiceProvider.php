<?php

namespace App\Repositories\Api\Public\ResourceGroup;

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
        $this->app->bind('App\Repositories\Api\Public\ResourceGroup\ResourceGroupInterface', 'App\Repositories\Api\Public\ResourceGroup\ResourceGroupRepository');
    }
}
