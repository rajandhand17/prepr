<?php

namespace App\Repositories\Api\Manage\ChallengeAchievement;

use Illuminate\Support\ServiceProvider;

class ChallengeAchievementServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\ChallengeAchievement\ChallengeAchievementInterface', 'App\Repositories\Api\Manage\ChallengeAchievement\ChallengeAchievementRepository');
    }
}
