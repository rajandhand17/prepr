<?php

namespace App\Repositories\Api\Dashboard\User;

use Illuminate\Support\ServiceProvider;

class UserDashboardServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Dashboard\User\UserDashboardInterface', 'App\Repositories\Api\Dashboard\User\UserDashboardRepository');
    }
}
