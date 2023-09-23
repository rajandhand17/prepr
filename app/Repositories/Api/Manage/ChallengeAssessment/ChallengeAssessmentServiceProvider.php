<?php

namespace App\Repositories\Api\Manage\challengeAssessmentService;

use Illuminate\Support\ServiceProvider;

class challengeAssessmentServiceServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\Api\Manage\challengeAssessmentService\challengeAssessmentServiceInterface', 'App\Repositories\Api\Manage\challengeAssessmentService\challengeAssessmentServiceRepository');
    }
}
