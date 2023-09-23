<?php

namespace App\Repositories\Api\Manage\ChallengeSkillsGroupsStack;

use Illuminate\Support\ServiceProvider;

class ChallengeSkillsGroupsStackServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\ChallengeSkillsGroupsStack\ChallengeSkillsGroupsStackInterface', 'App\Repositories\Api\Manage\ChallengeSkillsGroupsStack\ChallengeSkillsGroupsStackRepository');
    }
}
