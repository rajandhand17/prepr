<?php

namespace App\Repositories\Api\Public\Lab;

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
        $this->app->bind('App\Repositories\Api\Public\Lab\LabInterface', 'App\Repositories\Api\Public\Lab\LabRepository');
    }
}
