<?php

namespace App\Repositories\Api\Explore;

use Illuminate\Support\ServiceProvider;

class ExploreServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Leaderboard\LeaderboardInterface', 'App\Repositories\Api\Leaderboard\LeaderboardRepository');
    }
}
