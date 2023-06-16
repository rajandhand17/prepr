<?php

namespace App\Repositories\Api\Master;

use Illuminate\Support\ServiceProvider;

class MasterServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Master\MasterInterface', 'App\Repositories\Api\Master\MasterRepository');
    }
}
