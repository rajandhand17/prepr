<?php

namespace App\Repositories\Api\Manage\ChallengeSponsor;

use Illuminate\Support\ServiceProvider;

class ChallengeSponsorServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\ChallengeSponsor\ChallengeSponsorInterface', 'App\Repositories\Api\Manage\ChallengeSponsor\ChallengeSponsorRepository');
    }
}
