<?php

namespace App\Repositories\Api\Dashboard\Lab;

use Illuminate\Support\ServiceProvider;

class LabDashboardServiceProvider extends ServiceProvider
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
    public function register(): void
    {
        $this->app->bind('App\Repositories\Api\Dashboard\Lab\LabDashboardInterface', 'App\Repositories\Api\Dashboard\Lab\LabDashboardRepository');
    }
}
