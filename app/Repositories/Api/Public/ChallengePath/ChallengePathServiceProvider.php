<?php

namespace App\Repositories\Api\Public\ChallengePath;

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
        $this->app->bind('App\Repositories\Api\Public\ChallengePath\ChallengePathInterface', 'App\Repositories\Api\Public\ChallengePath\ChallengePathRepository');
    }
}
