<?php

namespace App\Repositories\Api\Dashboard\Organization;

use Illuminate\Support\ServiceProvider;

class OrganizationDashboardServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Dashboard\Organization\OrganizationDashboardInterface', 'App\Repositories\Api\Dashboard\Organization\OrganizationDashboardRepository');
    }
}
