<?php

namespace App\Repositories\Api\Manage\Report\Organization;

use Illuminate\Support\ServiceProvider;

class OrganizationReportServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\Report\Organization\OrganizationReportInterface', 'App\Repositories\Api\Manage\Report\Organization\OrganizationReportRepository');
    }
}
