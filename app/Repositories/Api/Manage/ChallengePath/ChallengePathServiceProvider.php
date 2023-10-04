<?php

namespace App\Repositories\Api\Manage\ChallengePath;

use Illuminate\Support\ServiceProvider;

class ChallengePathServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\ChallengePath\ChallengePathInterface', 'App\Repositories\Api\Manage\ChallengePath\ChallengePathRepository');
    }
}
