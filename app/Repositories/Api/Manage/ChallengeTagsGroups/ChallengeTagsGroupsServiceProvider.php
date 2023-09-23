<?php

namespace App\Repositories\Api\Manage\ChallengeTagsGroups;

use Illuminate\Support\ServiceProvider;

class ChallengeTagsGroupsServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\ChallengeTagsGroups\ChallengeTagsGroupsInterface', 'App\Repositories\Api\Manage\ChallengeTagsGroups\ChallengeTagsGroupsRepository');
    }
}
