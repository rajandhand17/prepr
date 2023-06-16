<?php

namespace App\Repositories\Api\MemberManagement;

use Illuminate\Support\ServiceProvider;

class MemberManagementServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\MemberManagement\MemberManagementInterface', 'App\Repositories\Api\MemberManagement\MemberManagementRepository');
    }
}
