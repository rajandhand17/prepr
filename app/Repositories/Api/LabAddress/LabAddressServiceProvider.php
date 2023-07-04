<?php

namespace App\Repositories\Api\LabAddress;

use Illuminate\Support\ServiceProvider;

class LabAddressServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\LabAddress\LabAddressInterface', 'App\Repositories\Api\LabAddress\LabAddressRepository');
    }
}
