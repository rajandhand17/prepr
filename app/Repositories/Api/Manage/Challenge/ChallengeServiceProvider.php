<?php

namespace App\Repositories\Api\Manage\Challenge;

use Illuminate\Support\ServiceProvider;

class ChallengeServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\Challenge\ChallengeInterface', 'App\Repositories\Api\Manage\Challenge\ChallengeRepository');
    }
}
