<?php

namespace App\Repositories\Api\Public\Challenge;

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
        $this->app->bind('App\Repositories\Api\Public\Challenge\ChallengeInterface', 'App\Repositories\Api\Public\Challenge\ChallengeRepository');
    }
}
