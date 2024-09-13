<?php

namespace App\Repositories\Api\Manage\Report\Lab;

use Illuminate\Support\ServiceProvider;

class LabReportServiceProvider extends ServiceProvider
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
        $this->app->bind(
            'App\Repositories\Api\Manage\Report\Lab\LabReportInterface',
            'App\Repositories\Api\Manage\Report\Lab\LabReportRepository'
        );
    }
}
