<?php

namespace App\Repositories\Api\Manage\LabMarketplace;

use Illuminate\Support\ServiceProvider;

class LabMarketplaceServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\LabMarketplace\LabMarketplaceInterface', 'App\Repositories\Api\Manage\LabMarketplace\LabMarketplaceRepository');
    }
}
