<?php

namespace App\Repositories\Api\Manage\LabAchievement;

use Illuminate\Support\ServiceProvider;

class LabAchievementServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\LabAchievement\LabAchievementInterface', 'App\Repositories\Api\Manage\LabAchievement\LabAchievementRepository');
    }
}
