<?php

namespace App\Repositories\Api\Manage\LabProgramAchievement;

use Illuminate\Support\ServiceProvider;

class LabProgramAchievementServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\LabProgramAchievement\LabProgramAchievementInterface', 'App\Repositories\Api\Manage\LabProgramAchievement\LabProgramAchievementRepository');
    }
}
