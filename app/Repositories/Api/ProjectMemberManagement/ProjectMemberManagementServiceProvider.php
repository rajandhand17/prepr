<?php

namespace App\Repositories\Api\ProjectMemberManagement;

use Illuminate\Support\ServiceProvider;

class ProjectMemberManagementServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\ProjectMemberManagement\ProjectMemberManagementInterface', 'App\Repositories\Api\ProjectMemberManagement\ProjectMemberManagementRepository');
    }
}
