<?php

namespace App\Repositories\Api\Public\Achievement;

use Illuminate\Support\ServiceProvider;

class AchievementServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Public\Achievement\AchievementInterface', 'App\Repositories\Api\Public\Achievement\AchievementRepository');
    }
}
