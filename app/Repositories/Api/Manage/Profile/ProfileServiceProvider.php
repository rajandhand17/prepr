<?php

namespace App\Repositories\Api\Manage\Profile;

use Illuminate\Support\ServiceProvider;

class ProfileServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\Profile\ProfileInterface', 'App\Repositories\Api\Manage\Profile\ProfileRepository');
    }
}
