<?php

namespace App\Repositories\Api\Manage\CampusConnect;

use Illuminate\Support\ServiceProvider;

class CampusConnectServiceProvider extends ServiceProvider
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
        $this->app->bind(CampusConnectInterface::class, CampusConnectRepository::class);
    }
}
