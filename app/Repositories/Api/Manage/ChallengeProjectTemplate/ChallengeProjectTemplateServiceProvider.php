<?php

namespace App\Repositories\Api\Manage\ChallengeProjectTemplateService;

use Illuminate\Support\ServiceProvider;

class ChallengeProjectTemplateService extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\ChallengeProjectTemplateService\ChallengeProjectTemplateService', 'App\Repositories\Api\Manage\ChallengeProjectTemplateService\ChallengeProjectTemplateService');
    }
}
