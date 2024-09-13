<?php

namespace App\Repositories\Api\Manage\Lab;

use Illuminate\Support\ServiceProvider;

class LabServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\Lab\LabInterface', 'App\Repositories\Api\Manage\Lab\LabRepository');
    }
}
