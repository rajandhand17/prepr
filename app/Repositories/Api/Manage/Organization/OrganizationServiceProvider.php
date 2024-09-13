<?php

namespace App\Repositories\Api\Manage\Organization;

use Illuminate\Support\ServiceProvider;

class OrganizationServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\Organization\OrganizationInterface', 'App\Repositories\Api\Manage\Organization\OrganizationRepository');
    }
}
