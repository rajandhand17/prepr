<?php

namespace App\Repositories\Api\Manage\ChallengeRequirement;

use Illuminate\Support\ServiceProvider;

class ChallengeRequirementServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\ChallengeRequirement\ChallengeRequirementInterface', 'App\Repositories\Api\Manage\ChallengeRequirement\ChallengeRequirementRepository');
    }
}
