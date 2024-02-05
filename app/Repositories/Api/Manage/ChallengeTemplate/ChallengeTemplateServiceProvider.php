<?php

namespace App\Repositories\Api\Manage\ChallengeTemplate;

use Illuminate\Support\ServiceProvider;

class ChallengeTemplateServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\ChallengeTemplate\ChallengeTemplateInterface', 'App\Repositories\Api\Manage\ChallengeTemplate\ChallengeTemplateRepository');
    }
}
