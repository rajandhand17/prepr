<?php

namespace App\Repositories\Api\Public\InvitationManagement;

use Illuminate\Support\ServiceProvider;

class InvitationManagementServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Public\InvitationManagement\InvitationManagementInterface', 'App\Repositories\Api\Public\InvitationManagement\InvitationManagementRepository');
    }
}
