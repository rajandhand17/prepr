<?php

namespace App\Repositories\Api\Manage\ProjectMemberManagement;

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
        $this->app->bind('App\Repositories\Api\Manage\ProjectMemberManagement\ProjectMemberManagementInterface', 'App\Repositories\Api\Manage\ProjectMemberManagement\ProjectMemberManagementRepository');
    }
}
