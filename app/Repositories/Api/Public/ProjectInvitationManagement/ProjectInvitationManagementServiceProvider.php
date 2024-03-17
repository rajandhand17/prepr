<?php

namespace App\Repositories\Api\Public\ProjectInvitationManagement;

use Illuminate\Support\ServiceProvider;

class ProjectInvitationManagementServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Public\ProjectInvitationManagement\ProjectInvitationManagementInterface', 'App\Repositories\Api\Public\ProjectInvitationManagement\ProjectInvitationManagementRepository');
    }
}
