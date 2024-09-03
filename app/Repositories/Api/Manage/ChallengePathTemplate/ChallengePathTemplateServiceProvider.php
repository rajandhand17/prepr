<?php

namespace App\Repositories\Api\Manage\ChallengePathTemplate;

use Illuminate\Support\ServiceProvider;

class ChallengePathTemplateServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\ChallengePathTemplate\ChallengePathTemplateInterface', 'App\Repositories\Api\Manage\ChallengePathTemplate\ChallengePathTemplateRepository');
    }
}
